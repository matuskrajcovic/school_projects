<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomepageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResultsController;

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

Route::get('/', [
	HomepageController::class, 'index'
])->name('homepage');


Route::middleware('can:adminAccess')->group(function() {
	#Route::get('/admin', [AdminController::class, 'index'])->name('admin')->middleware('auth');
	#Route::get('/admin/create', [AdminController::class, 'create_product']);
	#Route::get('/admin/edit', [AdminController::class, 'edit_product']);
	Route::get('/admin', [ProductController::class, 'index'])->name('admin');
	Route::get('/admin/create/{type}', [ProductController::class, 'create'])->name('create-product');
	Route::post('/admin/create/{type}', [ProductController::class, 'store'])->name('store-product');
	Route::get('/admin/{id}', [ProductController::class, 'edit'])->name('edit-product');
	Route::put('/admin/{id}', [ProductController::class, 'update'])->name('update-product');
	Route::delete('/admin/{id}', [ProductController::class, 'destroy'])->name('delete-product');

	Route::delete('/admin/delete-main-image/{id}', [ProductController::class, 'delete_main_image'])->name('delete_main_image');
	Route::delete('/admin/delete-other-image/{id}', [ProductController::class, 'delete_other_image'])->name('delete_other_image');
});

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('product/{id}', [ProductController::class, 'show'])->name('product');

Route::get('category/{id}', [ResultsController::class, 'show'])->name('category');

Route::get('results', [ResultsController::class, 'index'])->name('results');

Route::post('add-to-cart', [CartController::class, 'add_to_cart'])->name('add-to-cart');
Route::post('update-product', [CartController::class, 'update_cart_product'])->name('update-product');
Route::post('delete-from-cart', [CartController::class, 'delete_from_cart'])->name('delete-from-cart');
Route::match(['GET', 'POST'], 'cart', [CartController::class, 'cart'])->name('cart');
Route::match(['GET', 'POST'], 'cart-contact', [CartController::class, 'cart_contact'])->name('cart-contact');
Route::match(['GET', 'POST'], 'cart-shipping', [CartController::class, 'cart_shipping'])->name('cart-shipping');
Route::match(['GET', 'POST'], 'cart-payment', [CartController::class, 'cart_payment'])->name('cart-payment');
Route::match(['GET', 'POST'], 'cart-summary', [CartController::class, 'cart_summary'])->name('cart-summary');
Route::get('order-success', function () {return view('cart.order-success');})->name('order-success');

require __DIR__.'/auth.php';
