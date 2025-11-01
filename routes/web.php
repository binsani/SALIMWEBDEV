<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop & Products
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/lgas/{state}', [CheckoutController::class, 'getLgas'])->name('checkout.lgas');

    // Paystack Payment
    Route::get('/payment/{order}', [PaystackController::class, 'pay'])->name('paystack.pay');
    Route::get('/payment/callback', [PaystackController::class, 'callback'])->name('paystack.callback');

    // Customer Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');

    // Digital Downloads
    Route::get('/download/{token}', [DownloadController::class, 'download'])->name('download');
});

// Paystack Webhook (no auth required)
Route::post('/paystack/webhook', [PaystackController::class, 'webhook'])->name('paystack.webhook');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');

    // Categories
    Route::resource('categories', AdminCategoryController::class);

    // Orders
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
