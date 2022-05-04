<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/v1/token', [\App\Http\Controllers\Auth\Proxy\BBProxyController::class, 'getToken']);
Route::post('/v1/boletos', [\App\Http\Controllers\Auth\Proxy\BBProxyController::class, 'boletos'])->middleware('check.bb.token');
