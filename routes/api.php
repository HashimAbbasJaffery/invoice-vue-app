<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/invoices", [InvoiceController::class, "get_all_invoices"]);
Route::get("/search_invoice", [InvoiceController::class, "search"]);
Route::get("/create/invoice", [InvoiceController::class, "create_invoice"]);
Route::get("/customers", [CustomerController::class, "customers"]);
Route::get("/products", [ProductController::class, "products"]);