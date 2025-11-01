<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDownload;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['webhook']);
    }

    /**
     * Initialize Paystack payment.
     */
    public function pay(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Check if order is already paid
        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'This order has already been paid.');
        }

        // Create or get existing transaction
        $transaction = Transaction::firstOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => auth()->id(),
                'reference' => 'TXN-' . strtoupper(Str::random(16)),
                'amount' => $order->total_amount * 100, // Convert to kobo
                'currency' => 'NGN',
                'status' => 'pending',
            ]
        );

        $paystackPublicKey = config('paystack.public_key', env('PAYSTACK_PUBLIC_KEY'));

        return view('paystack.pay', compact('order', 'transaction', 'paystackPublicKey'));
    }

    /**
     * Handle Paystack callback.
     */
    public function callback(Request $request)
    {
        $reference = $request->input('reference');

        if (!$reference) {
            return redirect()->route('home')
                ->with('error', 'Payment reference not found.');
        }

        // Verify transaction with Paystack
        $verification = $this->verifyTransaction($reference);

        if (!$verification['success']) {
            return redirect()->route('home')
                ->with('error', 'Payment verification failed: ' . $verification['message']);
        }

        $data = $verification['data'];

        // Find transaction
        $transaction = Transaction::where('reference', $reference)
            ->orWhere('paystack_reference', $data['reference'])
            ->first();

        if (!$transaction) {
            return redirect()->route('home')
                ->with('error', 'Transaction not found.');
        }

        // Update transaction
        DB::beginTransaction();
        try {
            $transaction->update([
                'paystack_reference' => $data['reference'],
                'status' => $data['status'] === 'success' ? 'success' : 'failed',
                'payment_channel' => $data['channel'] ?? null,
                'paid_at' => $data['status'] === 'success' ? now() : null,
                'authorization_code' => $data['authorization']['authorization_code'] ?? null,
                'paystack_response' => $data,
            ]);

            // Update order if payment successful
            if ($data['status'] === 'success') {
                $order = $transaction->order;
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'paid',
                ]);

                // Generate digital downloads
                $this->generateDigitalDownloads($order);

                DB::commit();

                // TODO: Send order confirmation email

                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Payment successful! Your order has been confirmed.');
            }

            DB::commit();

            return redirect()->route('home')
                ->with('error', 'Payment was not successful. Please try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paystack callback error: ' . $e->getMessage());
            
            return redirect()->route('home')
                ->with('error', 'An error occurred processing your payment.');
        }
    }

    /**
     * Handle Paystack webhook.
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('X-Paystack-Signature');
        $secretKey = config('paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);

        if ($signature !== $computedSignature) {
            Log::warning('Invalid Paystack webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        Log::info('Paystack webhook received', ['event' => $event, 'data' => $data]);

        // Handle charge.success event
        if ($event === 'charge.success') {
            DB::beginTransaction();
            try {
                $transaction = Transaction::where('paystack_reference', $data['reference'])
                    ->orWhere('reference', $data['reference'])
                    ->first();

                if ($transaction && !$transaction->webhook_processed) {
                    $transaction->update([
                        'status' => 'success',
                        'paid_at' => now(),
                        'webhook_processed' => true,
                        'paystack_response' => $data,
                    ]);

                    // Update order
                    if ($transaction->order) {
                        $transaction->order->update([
                            'payment_status' => 'paid',
                            'status' => 'paid',
                        ]);

                        // Generate digital downloads
                        $this->generateDigitalDownloads($transaction->order);
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Webhook processed'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Webhook processing error: ' . $e->getMessage());
                return response()->json(['error' => 'Processing failed'], 500);
            }
        }

        return response()->json(['message' => 'Event not handled'], 200);
    }

    /**
     * Verify transaction with Paystack API.
     */
    private function verifyTransaction($reference)
    {
        try {
            $secretKey = config('paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
            $url = "https://api.paystack.co/transaction/verify/{$reference}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$secretKey}",
                'Content-Type' => 'application/json',
            ])->get($url);

            $result = $response->json();

            if ($response->successful() && $result['status']) {
                return [
                    'success' => true,
                    'data' => $result['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Verification failed',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection error',
            ];
        }
    }

    /**
     * Generate digital downloads for an order.
     */
    private function generateDigitalDownloads(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->isDigital() && !$item->download) {
                OrderDownload::create([
                    'order_item_id' => $item->id,
                    'user_id' => $order->user_id,
                    'download_token' => Str::random(64),
                    'downloads_count' => 0,
                    'expires_at' => $item->download_expiry_hours 
                        ? now()->addHours($item->download_expiry_hours) 
                        : null,
                ]);
            }
        }
    }
}