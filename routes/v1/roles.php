<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::post('/assign-admin', [RoleController::class, 'assignAdmin']);
    Route::post('/revoke-admin', [RoleController::class, 'revokeAdmin']);
});

