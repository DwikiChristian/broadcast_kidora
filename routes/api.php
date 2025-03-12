<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImportController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('users', [UserController::class, 'store']);

Route::delete('users/{id}', [UserController::class, 'destroy']);

Route::patch('users/{id}', [UserController::class, 'update']);

Route::post('import-users', [ImportController::class, 'import']);

Route::get('/users/all', [UserController::class, 'showAll']); 

Route::get('/users/search', [UserController::class, 'search']);