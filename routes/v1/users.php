<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
});
