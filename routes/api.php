<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;


Route::post('/send', [NotificationController::class, 'send'])->middleware('throttle:300,60');
