<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;

Route::view('/chat', 'chat'); // Display the chat interface

Route::post('/chat', [ChatController::class, 'chat']); // Handle the chat POST request

use App\Http\Controllers\UploadController;

Route::view('/upload', 'upload'); // Display the upload interface

Route::post('/upload', [UploadController::class, 'upload']); // Handle the upload POST request


// Other routes...

