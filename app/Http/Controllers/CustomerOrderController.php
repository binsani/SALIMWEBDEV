<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display customer's orders.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display order details.
     */
    public function show($id)
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'items.download', 'transaction'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }
}