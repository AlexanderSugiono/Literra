<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FeaturedBookController;
use App\Http\Controllers\KasirOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/books/{book}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');

Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'kasir') {
        return redirect()->route('kasir.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard-admin');
    })->name('admin.dashboard');

    Route::resource('/admin/books', BookController::class)
        ->names([
            'index' => 'admin.books.index',
            'create' => 'admin.books.create',
            'store' => 'admin.books.store',
            'edit' => 'admin.books.edit',
            'update' => 'admin.books.update',
            'destroy' => 'admin.books.destroy',
        ])
        ->except(['show']);

    Route::resource('/admin/categories', CategoryController::class)
        ->names([
            'index' => 'admin.categories.index',
            'store' => 'admin.categories.store',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ])
        ->except(['create', 'edit', 'show']);

    Route::resource('/admin/coupons', CouponController::class)
        ->names([
            'index' => 'admin.coupons.index',
            'store' => 'admin.coupons.store',
            'update' => 'admin.coupons.update',
            'destroy' => 'admin.coupons.destroy',
        ])
        ->except(['create', 'edit', 'show']);

    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');

    Route::get('/admin/featured-books', [FeaturedBookController::class, 'index'])->name('admin.featured.index');
    Route::post('/admin/featured-books', [FeaturedBookController::class, 'store'])->name('admin.featured.store');
    Route::post('/admin/featured-books/{featuredBook}', [FeaturedBookController::class, 'update'])->name('admin.featured.update');
    Route::delete('/admin/featured-books/{featuredBook}', [FeaturedBookController::class, 'destroy'])->name('admin.featured.destroy');
    Route::post('/admin/featured-books/{featuredBook}/move-up', [FeaturedBookController::class, 'moveUp'])->name('admin.featured.move-up');
    Route::post('/admin/featured-books/{featuredBook}/move-down', [FeaturedBookController::class, 'moveDown'])->name('admin.featured.move-down');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', function () {
        return view('dashboard-kasir');
    })->name('kasir.dashboard');

    Route::get('/kasir/pos', [PosController::class, 'index'])->name('kasir.pos');
    Route::post('/kasir/pos/add', [PosController::class, 'addItem'])->name('kasir.pos.add');
    Route::post('/kasir/pos/update', [PosController::class, 'updateItem'])->name('kasir.pos.update');
    Route::post('/kasir/pos/remove', [PosController::class, 'removeItem'])->name('kasir.pos.remove');
    Route::post('/kasir/pos/checkout', [PosController::class, 'checkout'])->name('kasir.pos.checkout');
    Route::post('/kasir/pos/apply-coupon', [PosController::class, 'applyCoupon'])->name('kasir.pos.apply-coupon');
    Route::post('/kasir/pos/remove-coupon', [PosController::class, 'removeCoupon'])->name('kasir.pos.remove-coupon');

    Route::get('/kasir/orders', [KasirOrderController::class, 'index'])->name('kasir.orders.index');
    Route::get('/kasir/orders/search', [KasirOrderController::class, 'search'])->name('kasir.orders.search');
    Route::get('/kasir/orders/{order}', [KasirOrderController::class, 'show'])->name('kasir.orders.show');
    Route::post('/kasir/orders/{order}/process', [KasirOrderController::class, 'process'])->name('kasir.orders.process');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/{cart}/quantity', [CartController::class, 'updateQuantity'])->name('cart.quantity');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::get('/order/success/{order_code}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

require __DIR__.'/auth.php';
