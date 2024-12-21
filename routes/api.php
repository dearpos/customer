<?php

use Dearpos\Customer\Http\Controllers\CustomerController;
use Dearpos\Customer\Http\Controllers\CustomerGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::apiResource('customers', CustomerController::class);

    Route::prefix('customers')->group(function () {
        Route::apiResource('customer-groups', CustomerGroupController::class);
    });
});
