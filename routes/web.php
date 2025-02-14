<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Site\AccountController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CheckoutController;

Route::view('/', 'site.pages.homepage');

Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('register', [LoginController::class, 'register'])->name('register.post');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/category/{slug}', [\App\Http\Controllers\Site\CategoryController::class, 'show'])->name('category.show');

Route::get('/product/{slug}', [\App\Http\Controllers\Site\ProductController::class, 'show'])->name('product.show');
Route::post('/product/add/cart', [\App\Http\Controllers\Site\ProductController::class, 'addToCart'])->name('product.add.cart');

Route::get('/cart', [CartController::class, 'getCart'])->name('checkout.cart');
Route::get('/cart/item/{id}/remove', [CartController::class, 'removeItem'])->name('checkout.cart.remove');
Route::get('/cart/clear', [CartController::class, 'clearCart'])->name('checkout.cart.clear');
/*
Currently we are not attaching the shopping cart to authenticated user. The package we have used to add shopping cart functionality does support the user-specific shopping cart. For that, you have to move the add to cart route and all shopping cart route under the route group which is using the auth middleware.
I want to keep this series as simple as possible so won’t be going much deep into each and every detail. Check the package documentation for using user-specific shopping carts.
*/

Route::group(['middleware' => ['auth']], function () {
    Route::get('/checkout', [CheckoutController::class, 'getCheckout'])->name('checkout.index');
    Route::post('/checkout/order', [CheckoutController::class, 'placeOrder'])->name('checkout.place.order');
    Route::get('checkout/payment/complete', [CheckoutController::class, 'complete'])->name('checkout.payment.complete');

    Route::get('account/orders', [AccountController::class, 'getOrders'])->name('account.orders');
});

/* Admin */
Route::group(['prefix'  =>  'admin', 'middleware' => ['auth', 'admin']], function () {

    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/{order}/show', [OrderController::class, 'show'])->name('admin.orders.show');
     });

    Route::group(['prefix'  =>   'categories'], function() {

        Route::get('/', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/store', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::post('/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
        Route::get('/{id}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'delete'])->name('admin.categories.delete');
    
    });

    Route::group(['prefix'  =>   'attributes'], function() {

        Route::get('/', [AttributeController::class, 'index'])->name('admin.attributes.index');
        Route::get('/create', [AttributeController::class, 'create'])->name('admin.attributes.create');
        Route::post('/store', [AttributeController::class, 'store'])->name('admin.attributes.store');
        Route::get('/{id}/edit', [AttributeController::class, 'edit'])->name('admin.attributes.edit');
        Route::post('/update', [AttributeController::class, 'update'])->name('admin.attributes.update');
        Route::get('/{id}/delete', [AttributeController::class, 'delete'])->name('admin.attributes.delete');

        Route::post('/get-values', [AttributeValueController::class, 'getValues']);
        Route::post('/add-values', [AttributeValueController::class, 'addValues']);
        Route::post('/update-values', [AttributeValueController::class, 'updateValues']);
        Route::post('/delete-values', [AttributeValueController::class, 'deleteValues']);
    
    });

    Route::group(['prefix' => 'brands'], function() {

        Route::get('/', [BrandController::class, 'index'])->name('admin.brands.index');
        Route::get('/create', [BrandController::class, 'create'])->name('admin.brands.create');
        Route::post('/store', [BrandController::class, 'store'])->name('admin.brands.store');
        Route::get('/{id}/edit', [BrandController::class, 'edit'])->name('admin.brands.edit');
        Route::post('/update', [BrandController::class, 'update'])->name('admin.brands.update');
        Route::get('/{id}/delete', [BrandController::class, 'delete'])->name('admin.brands.delete');

    });

    Route::group(['prefix' => 'products'], function () {

        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/store', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
        Route::post('/update', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');

        Route::post('images/upload', [ProductImageController::class, 'upload'])->name('admin.products.images.upload');
        Route::get('images/{id}/delete', [ProductImageController::class, 'delete'])->name('admin.products.images.delete');

        // Load attributes on the page load
        Route::get('attributes/load', [ProductAttributeController::class, 'loadAttributes']);
        // Load product attributes on the page load
        Route::post('attributes', [ProductAttributeController::class, 'productAttributes']);
        // Load option values for a attribute
        Route::post('attributes/values', [ProductAttributeController::class, 'loadValues']);
        // Add product attribute to the current product
        Route::post('attributes/add', [ProductAttributeController::class, 'addAttribute']);
        // Delete product attribute from the current product
        Route::post('attributes/delete', [ProductAttributeController::class, 'deleteAttribute']);
     
     });

});