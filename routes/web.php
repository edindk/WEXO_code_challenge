<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieAndSeriesController;
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

Route::get('/', [GenreController::class, 'create_genre_obj']);
Route::get('/movies-and-series/{genre}', [MovieAndSeriesController::class, 'show_by_genre']);
Route::get('movie/{id}', [MovieAndSeriesController::class, 'show_movie']);
Route::get('showall/{genre}', [MovieAndSeriesController::class, 'show_all']);
