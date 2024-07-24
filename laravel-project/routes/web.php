<?php

use Illuminate\Support\Facades\Route;

// Other routes...
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ChatController;

Route::get('/login', [UserController::class, 'index'])->name('login.page');
Route::post('/login', [UserController::class, 'login'])->name('login');
// Route::post('/add', [UserController::class, 'add']);
// Route::get('/delete/{uid}', [UserController::class, 'delete']);
// Route::get('/edit/{uid}', [UserController::class, 'edit']);
// Route::post('/edit/{uid}', [UserController::class, 'update']);

Route::get('/home', [LogController::class, 'index'])->name('home.page');
Route::post('/home/add', [LogController::class, 'add'])->name('home.add');
Route::post('/home/retrieve/{logid}', [LogController::class, 'retrieve'])->name('home.retrieve');
Route::get('/home/delete/{logid}', [LogController::class, 'delete'])->name('home.delete');
Route::get('/home/edit/{logid}', [LogController::class, 'edit'])->name('home.edit');
Route::post('/home/edit/{logid}', [LogController::class, 'update'])->name('home.update');

Route::get('/chat', [ChatController::class, 'index'])->name('chat.page');
Route::post('/chat', [ChatController::class, 'chat'])->name('chat');