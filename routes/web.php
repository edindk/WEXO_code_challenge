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

Route::get('/', [GenreController::class, 'show_homepage']);
Route::get('movies-and-series/{genre}/{range?}', [MovieAndSeriesController::class, 'show_by_genre']);
Route::get('movie/{id}', [MovieAndSeriesController::class, 'show_movie_info']);
Route::get('showall/{genre}', [MovieAndSeriesController::class, 'show_by_genre']);
Route::get('wishlist', [MovieAndSeriesController::class, 'show_wishlist']);
Route::get('addtowishlist/{id}', [MovieAndSeriesController::class, 'add_to_wishlist']);

