<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LogController;

Route::apiResource('chats', ChatController::class);
Route::apiResource('collections', CollectionController::class);
Route::apiResource('documents', DocumentController::class);
Route::apiResource('logs', LogController::class);

Route::middleware('api')->group(function () {

    Route::post('/service/collection', function () {
        try {
            $response = Http::post('http://localhost:5000/', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to create collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while creating collection'], 500);
        }
    });

    Route::put('/service/collection', function () {
        try {
            $response = Http::put('http://localhost:5000/', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to write to collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while writing to collection'], 500);
        }
    });

    Route::post('/service/chat', function () {
        try {
            $response = Http::post('http://localhost:5000/chat', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to chat'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while processing chat'], 500);
        }
    });

    Route::delete('/service/collection', function () {
        try {
            $response = Http::delete('http://localhost:5000/', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to clear collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while clearing collection'], 500);
        }
    });

});