<?php

use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//for student section
Route::get('/', [StudentController::class, 'index']);
Route::get('list-student', [StudentController::class, 'index']);
Route::post('store-student', [StudentController::class, 'store']);
Route::post('edit-student', [StudentController::class, 'edit']);
Route::post('delete-student', [StudentController::class, 'destroy']);

//for score section
Route::get('scores', [ScoreController::class, 'index']);
Route::get('list-score', [ScoreController::class, 'index']);
Route::post('store-score', [ScoreController::class, 'store']);
Route::post('edit-score', [ScoreController::class, 'edit']);
Route::post('delete-score', [ScoreController::class, 'destroy']);
