<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show checkout form.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Calculate cart totals
        $subtotal = 0;
        $cartItems = [];

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product && $product->is_active && $product->inStock()) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ];
                $subtotal += $product->price * $item['quantity'];
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Cart items are no longer available.');
        }

        $states = State::orderBy('name')->get();
        $deliveryFees = [
            'lagos' => Setting::get('delivery_fee_lagos', 2000),
            'nationwide' => Setting::get('delivery_fee_nationwide', 4000),
            'pickup' => Setting::get('delivery_fee_pickup', 0),
        ];

        return view('checkout.index', compact('cartItems', 'subtotal', 'states', 'deliveryFees'));
    }

    /**
     * Get LGAs for a state (AJAX).
     */
    public function getLgas($stateId)
    {
        $lgas = Lga::where('state_id', $stateId)->orderBy('name')->get();
        return response()->json($lgas);
    }

    /**
     * Process checkout and create order.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'delivery_option' => 'required|in:lagos,nationwide,pickup',
            'delivery_full_name' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'delivery_address' => 'required_unless:delivery_option,pickup|string',
            'delivery_city' => 'required_unless:delivery_option,pickup|string|max:255',
            'delivery_state_id' => 'required_unless:delivery_option,pickup|exists:states,id',
            'delivery_lga_id' => 'required_unless:delivery_option,pickup|exists:lgas,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $id => $item) {
                $product = Product::lockForUpdate()->find($id);
                
                if (!$product || !$product->is_active) {
                    throw new \Exception("Product '{$item['name']}' is no longer available.");
                }

                if (!$product->inStock()) {
                    throw new \Exception("Product '{$product->name}' is out of stock.");
                }

                // Check stock for physical products
                if ($product->isPhysical() && $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for '{$product->name}'. Only {$product->stock_quantity} available.");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate delivery fee
            $deliveryFee = 0;
            switch ($validated['delivery_option']) {
                case 'lagos':
                    $deliveryFee = Setting::get('delivery_fee_lagos', 2000);
                    break;
                case 'nationwide':
                    $deliveryFee = Setting::get('delivery_fee_nationwide', 4000);
                    break;
                case 'pickup':
                    $deliveryFee = 0;
                    break;
            }

            $totalAmount = $subtotal + $deliveryFee;

            // Get state and LGA names
            $state = null;
            $lga = null;
            if ($request->filled('delivery_state_id')) {
                $state = State::find($validated['delivery_state_id']);
            }
            if ($request->filled('delivery_lga_id')) {
                $lga = Lga::find($validated['delivery_lga_id']);
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_reference' => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'delivery_option' => $validated['delivery_option'],
                'delivery_full_name' => $validated['delivery_full_name'],
                'delivery_phone' => $validated['delivery_phone'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_city' => $validated['delivery_city'] ?? null,
                'delivery_state' => $state ? $state->name : null,
                'delivery_lga' => $lga ? $lga->name : null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and reduce stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_type' => $item['product']->product_type,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'digital_file_path' => $item['product']->digital_file_path,
                    'download_limit' => $item['product']->download_limit,
                    'download_expiry_hours' => $item['product']->download_expiry_hours,
                ]);

                // Reduce stock for physical products
                if ($item['product']->isPhysical()) {
                    $item['product']->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Clear cart
            session()->forget('cart');

            // Redirect to Paystack payment
            return redirect()->route('paystack.pay', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
}