<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;

Route::apiResource('chats', ChatController::class);
Route::apiResource('documents', DocumentController::class);

Route::middleware('api')->group(function () {

    Route::post('/rest/create', function () {
        try {
            $response = Http::post('http://localhost:5000/create', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to create collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while creating collection'], 500);
        }
    });

    Route::post('/rest/write', function () {
        try {
            $response = Http::post('http://localhost:5000/write', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to write document'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while writing document'], 500);
        }
    });

    Route::post('/rest/chat', function () {
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

    Route::post('/rest/delete', function () {
        try {
            $response = Http::post('http://localhost:5000/delete', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to clean collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while cleaning collection'], 500);
        }
    });

});
