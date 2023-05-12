<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) { return $request->user(); });

    
Route::get('/movies',[MovieController::class,'index']);
Route::get('/movies/{id}',[MovieController::class,'indexById']);
Route::post('/movies',[MovieController::class,'store']);
Route::put('/movies/{id}',[MovieController::class,'update']);
Route::delete('/movies/{id}',[MovieController::class,'destroy']);

Route::get('/movies/{movieId}/shifts',[ShiftController::class,'indexMS']);
Route::post('/movies/{movieId}/shifts',[ShiftController::class,'storeMS']);

Route::get('/shifts',[ShiftController::class,'index']);
Route::get('/shifts/{id}',[ShiftController::class,'indexById']);
Route::post('/shifts',[ShiftController::class,'store']);
Route::put('/shifts/{id}',[ShiftController::class,'update']);
Route::delete('/shifts/{id}',[ShiftController::class,'destroy']);

