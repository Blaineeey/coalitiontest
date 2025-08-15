<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Return all products (GET /api/products)
Route::get('/products', [ProductController::class, 'index']);

// Store a new product (POST /api/products)
Route::post('/products', [ProductController::class, 'store']);

// Update a product (PUT /api/products/{id})
Route::put('/products/{id}', [ProductController::class, 'update']);
