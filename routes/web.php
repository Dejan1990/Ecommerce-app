<?php

use App\Contracts\BrandContract;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\SettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['prefix'  =>  'admin', 'middleware' => ['auth', 'admin']], function () {

    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::group(['prefix'  =>   'categories'], function() {

        Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/store', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::post('/update', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::get('/{id}/delete', [CategoryController::class, 'delete'])->name('admin.categories.delete');
    
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

        Route::get('/', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::post('/update', [ProductController::class, 'update'])->name('admin.products.update');

        Route::post('images/upload', [ProductImageController::class, 'upload'])->name('admin.products.images.upload');
        Route::get('images/{id}/delete', [ProductImageController::class, 'delete'])->name('admin.products.images.delete');
     
     });

});