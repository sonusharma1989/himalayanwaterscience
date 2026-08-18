<?php

use Hws\FieldService\Http\Controllers\Storefront\CustomerAccountController;
use Hws\FieldService\Http\Controllers\Storefront\CustomerRequestController;
use Hws\FieldService\Http\Controllers\Storefront\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'locale', 'theme', 'currency'])->group(function () {
    Route::get('services', [ServiceController::class, 'index'])->name('hws.services.index');
    Route::get('services/{slug}', [ServiceController::class, 'show'])->name('hws.services.show');
    Route::get('our-vision', [ServiceController::class, 'vision'])->name('hws.vision');

    Route::post('customer-requests', [CustomerRequestController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('hws.customer-requests.store');

    Route::get('customer/account/tracking', [CustomerAccountController::class, 'index'])
        ->middleware('customer')
        ->name('hws.customer.account.tracking');
    Route::get('customer/account/quotations/{id}/pdf', [CustomerAccountController::class, 'quotationPdf'])
        ->middleware('customer')
        ->name('hws.customer.account.quotations.pdf');
});
