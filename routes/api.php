<?php

use App\Http\Controllers\API\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('user-detail',           [AuthController::class, 'userDetail']);
    Route::get('/getListProcess',        [AuthController::class, 'getListProcess']);
    Route::get('/createDirectory/{name}',[AuthController::class, 'createDirectory']);
    //Route::post('/createFile',           [AuthController::class, 'createFile']);
    Route::get('/createFile/{name}',     [AuthController::class, 'createFile']);
    Route::get('/directoriesList',       [AuthController::class, 'getDirectoriesList']);
    Route::get('/filesList',             [AuthController::class, 'getFilesList']);
    Route::post('logout',                [AuthController::class, 'logout']);
});
