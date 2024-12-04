<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\CartController;
// use Illuminate\Routing\Route;
use Modules\Shop\Http\Controllers\ProductController;
use Modules\Shop\Http\Controllers\OrderController;
use Modules\Shop\Http\Controllers\PaymentController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{categorySlug}',[ProductController::class, 'category'])-> name('products.category');
Route::get('/tag/{tagSlug}',[ProductController::class,'tag'])->name('products.tag');

Route::post('/payments/midtrans', [PaymentController::class, "midtrans"])->name('payment.midtrans');


//product (ujicoba uploaad image ke 2)
// Route::get('prod', [ProdController::class, 'index'])->name('prod.index');
// Route::get('prod/create', [ProductController::class, 'create'])->name('products.create');
Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
Route::get('products/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::get('products/destroy', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('products/store', [ProductController::class, 'store'])->name('products.store');

//mengirim gambar upload 
Route::post('prod', [ProdController::class, 'store'])->name('prod.store');

Route::resource('prod', ProductController::class);




Route::middleware(['auth'])->group(function(){
    
    Route::get('/orders/checkout', [OrderController::class, 'checkout'])-> name ('orders.checkout');
    Route::post('/orders/checkout', [OrderController::class, 'store'])-> name ('orders.store');
    Route::post('/orders/shipping-fee', [OrderController::class, 'shippingFee'])-> name ('orders.shipping_fee');
    Route::post('/orders/choose-package', [OrderController::class, 'choosePackage'])-> name ('orders.choose_package');
    Route::get('/carts', [CartController::class, 'index'])-> name ('carts.index');
    Route::post('/carts', [CartController::class, 'store'])-> name ('carts.store');
    Route::get('/carts/{id}/remove', [CartController::class, 'destroy'])->name('carts.destroy');
    Route::post('/carts', [CartController::class, 'store'])->name('carts.store');
    Route::put('/carts', [CartController::class, 'update'])->name('carts.update');

});

Route::get('/{categorySlug}/{productSlug}', [ProductController::class, 'show'])->name ('products.show');


//     Route::prefix('shop')->group(function() {
//     Route::get('/', 'ShopController@index');
// });
