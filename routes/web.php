<?php

use App\Http\Controllers\GameController;
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
/** Menú inicial elección modo de juego **/
Route::get('/', function () {
    return view('menu');
}) ->name('menu');;

/** Lógica del panel de juego **/
Route::get('/game', [\App\Http\Controllers\GameController::class, 'index']);

Route::post('/game', [\App\Http\Controllers\GameController::class, 'saveGameState']);

Route::post('/game/new', [\App\Http\Controllers\GameController::class, 'newGame']);

Route::get('/game', [\App\Http\Controllers\GameController::class, 'typeGame'])
    ->name('game.type');







