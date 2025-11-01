<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        // Search by order reference or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'transaction']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        // If status is delivered and order has digital products, handle download generation
        if ($validated['status'] === 'delivered' && $order->hasDigitalProducts()) {
            $this->generateDigitalDownloads($order);
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        return back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        if ($order->isCancelled()) {
            return back()->with('error', 'Order is already cancelled.');
        }

        if ($order->status === 'delivered') {
            return back()->with('error', 'Cannot cancel a delivered order.');
        }

        // Restore stock for physical products
        foreach ($order->items as $item) {
            if ($item->isPhysical()) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Generate invoice for order.
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'transaction']);
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Generate digital downloads for an order.
     */
    private function generateDigitalDownloads(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->isDigital() && !$item->download) {
                \App\Models\OrderDownload::create([
                    'order_item_id' => $item->id,
                    'user_id' => $order->user_id,
                    'download_token' => \Str::random(64),
                    'downloads_count' => 0,
                    'expires_at' => $item->download_expiry_hours 
                        ? now()->addHours($item->download_expiry_hours) 
                        : null,
                ]);
            }
        }
    }
}
