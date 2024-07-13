<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\SendNotificationThrottleMiddleware;

Route::post('/send', [NotificationController::class, 'send'])
    ->middleware(SendNotificationThrottleMiddleware::class);
