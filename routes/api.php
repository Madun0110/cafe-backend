<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\guest\MenuController;
use App\Http\Controllers\guest\OrderController;
use App\Http\Controllers\adminsite\ProductsController;
use App\Http\Controllers\adminsite\CategoriesController;
use App\Http\Controllers\adminsite\DrinkOrderDetailController;
use App\Http\Controllers\adminsite\FoodOrderDetailController;

Route::prefix('categories')->group(function () {
    Route::get('',[CategoriesController::class,'getAllCategories']);
    Route::get('/{id}',[CategoriesController::class,'singleCategory']);
    Route::post('',[CategoriesController::class,'createCategory']);
    Route::put('/{id}',[CategoriesController::class,'updateCategory']);
    Route::delete('/{id}',[CategoriesController::class,'deleteCategory']);
});

Route::prefix('products')->group(function () {
    Route::get('',[ProductsController::class,'getAllProducts']);
    Route::get('/{id}',[ProductsController::class,'singleProduct']);
    Route::post('',[ProductsController::class,'createProduct']);
    Route::put('/{id}',[ProductsController::class,'updateProduct']);
    Route::delete('/{id}',[ProductsController::class,'deleteProduct']);
});

Route::prefix('kitchen')->group(function () {
    Route::get('/food',[FoodOrderDetailController::class,'getAllFoodOrderDetails']);
    Route::get('/drink',[DrinkOrderDetailController::class,'getAllDrinkOrderDetails']);
});

Route::prefix('guest')->group(function () {
    Route::get('/products',[MenuController::class,'getProducts']);
    Route::post('/order',[OrderController::class,'orderProducts']);
});
