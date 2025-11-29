<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        require __DIR__ . '/v1/auth.php';
    });

    Route::prefix('users')->group(function () {
        require __DIR__ . '/v1/users.php';
    });

    Route::prefix('roles')->group(function () {
        require __DIR__ . '/v1/roles.php';
    });
});
