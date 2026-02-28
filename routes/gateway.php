<?php

use App\Http\Controllers\Gateway\GatewayController;

Route::middleware('gateway.token')->group(function () {
    Route::match(
        ['GET', 'POST', 'PUT', 'DELETE'],
        '/gateway/products/{path?}',
        [GatewayController::class, 'handle']
    )->where('path', '.*');
});
