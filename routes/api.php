<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\adminsite\CategoriesController;
use App\Http\Controllers\adminsite\ProductsController;
use App\Http\Controllers\adminsite\OrdersController;
use App\Http\Controllers\adminsite\TransactionsController;

use App\Http\Controllers\guest\MenuController;
use App\Http\Controllers\guest\OrderController as GuestOrderController;

Route::post('/login',   [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout',  [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| GUEST (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('guest')->group(function () {
    Route::get('/menu',       [MenuController::class, 'getProducts']);
    Route::get('/categories',[CategoriesController::class, 'getAllCategories']);
    Route::post('/order',     [GuestOrderController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| ADMIN (JWT PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'role:admin,kasir'])->group(function () {

    /* ================= CATEGORIES ================= */
    Route::get('categories',        [CategoriesController::class, 'getAllCategories']);
    Route::get('categories/{id}',   [CategoriesController::class, 'singleCategory']);
    Route::post('categories',       [CategoriesController::class, 'createCategory']);
    Route::put('categories/{id}',   [CategoriesController::class, 'updateCategory']);
    Route::delete('categories/{id}',[CategoriesController::class, 'deleteCategory']);

    /* ================= PRODUCTS ================= */
    Route::get('products',        [ProductsController::class, 'getAllProducts']);
    Route::get('products/{id}',   [ProductsController::class, 'singleProduct']);
    Route::post('products',       [ProductsController::class, 'createProduct']);
    Route::put('products/{id}',   [ProductsController::class, 'updateProduct']);
    Route::delete('products/{id}',[ProductsController::class, 'deleteProduct']);

    /* ================= ORDERS ================= */
    Route::get('orders',        [OrdersController::class, 'index']);
    Route::get('orders/{id}',   [OrdersController::class, 'show']);
    Route::delete('orders/{id}', [OrdersController::class, 'destroy']);


    // status dapur (pending → processing → ready → completed)
    Route::patch(
        'orders/{id}/status',
        [OrdersController::class, 'updateStatus']
    );

    // status 1 item
    Route::patch(
        'orders/{orderId}/item/{index}',
        [OrdersController::class, 'updateItemStatus']
    );

    /* ================= TRANSACTIONS ================= */

    // list transaksi
    Route::get('transactions',      [TransactionsController::class, 'index']);
    Route::get('transactions/{id}', [TransactionsController::class, 'show']);

    // konfirmasi pembayaran (unpaid → paid)
    Route::post('transactions',     [TransactionsController::class, 'store']);

    // refund (paid → refunded)
    Route::post(
        'transactions/{id}/refund',
        [TransactionsController::class, 'refund']
    );
});
