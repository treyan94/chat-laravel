<?php

use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'users'])->middleware('guest')->name('users');

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/page1', [HomeController::class, 'page1'])->name('page1');
    Route::get('/page2', [HomeController::class, 'page2'])->name('page2');

    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    Route::post('/chat-rooms', [ChatRoomController::class, 'store'])->name('chat-rooms.store');
    Route::get('/chat-rooms/{chatRoom}', [ChatRoomController::class, 'show'])->name('chat-rooms.show');
});
