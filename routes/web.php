<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/belanja', [ShopController::class, 'index'])->name('shop.index');
Route::get('/api/produk', [ShopController::class, 'api'])->name('shop.api');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Otentikasi (khusus tamu)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Wajib login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/sukses/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Upload bukti pembayaran (Transfer Manual / QRIS)
    Route::post('/checkout/bukti-bayar/{order}', [CheckoutController::class, 'uploadProof'])
        ->name('checkout.upload-proof');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
});

/*
|--------------------------------------------------------------------------
| Panel admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', ProductController::class)
        ->parameters(['produk' => 'product'])
        ->except(['show']);

    Route::resource('kategori', CategoryController::class)
        ->parameters(['kategori' => 'category'])
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('pesanan.index');
    Route::patch('/pesanan/{order}', [AdminOrderController::class, 'update'])->name('pesanan.update');
});
