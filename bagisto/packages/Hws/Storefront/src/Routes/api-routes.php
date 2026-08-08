<?php

use Illuminate\Support\Facades\Route;
use Hws\Storefront\Http\Controllers\Api\ProductController;
use Hws\Storefront\Http\Controllers\Api\CategoryController;

/**
 * All routes here are public — no auth middleware applied, by design,
 * per requirement #2. Everything here is read-only catalog browsing;
 * nothing that touches a specific customer's data lives in this file.
 */
Route::prefix('api/storefront')->group(function () {

    Route::get('products', [ProductController::class, 'index'])
        ->name('hws.api.storefront.products.index');

    Route::get('products/{urlKey}', [ProductController::class, 'show'])
        ->name('hws.api.storefront.products.show');

    Route::get('categories', [CategoryController::class, 'index'])
        ->name('hws.api.storefront.categories.index');

    Route::get('categories/{slug}', [CategoryController::class, 'show'])
        ->name('hws.api.storefront.categories.show');

});
