<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product && $product->is_active) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ];
                $subtotal += $product->price * $item['quantity'];
            }
        }

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (!$product->is_active) {
            return back()->with('error', 'Product is not available.');
        }

        if (!$product->inStock()) {
            return back()->with('error', 'Product is out of stock.');
        }

        $quantity = $request->input('quantity', 1);

        // Check stock for physical products
        if ($product->isPhysical() && $product->stock_quantity < $quantity) {
            return back()->with('error', 'Insufficient stock. Only ' . $product->stock_quantity . ' available.');
        }

        $cart = session()->get('cart', []);

        // If product exists in cart, update quantity
        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $quantity;
            
            if ($product->isPhysical() && $product->stock_quantity < $newQuantity) {
                return back()->with('error', 'Cannot add more. Maximum stock: ' . $product->stock_quantity);
            }
            
            $cart[$id]['quantity'] = $newQuantity;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $quantity = $request->input('quantity', 1);

        if ($quantity < 1) {
            return back()->with('error', 'Quantity must be at least 1.');
        }

        $product = Product::findOrFail($id);

        // Check stock for physical products
        if ($product->isPhysical() && $product->stock_quantity < $quantity) {
            return back()->with('error', 'Insufficient stock. Only ' . $product->stock_quantity . ' available.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
            return back()->with('success', 'Cart updated successfully!');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return back()->with('success', 'Product removed from cart.');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }

    /**
     * Get cart count (for header display).
     */
    public function count()
    {
        $cart = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        return response()->json(['count' => $count]);
    }
}