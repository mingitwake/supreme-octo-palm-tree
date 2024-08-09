<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

use App\Http\Controllers\ChatController;
Route::apiResource('chats', ChatController::class);

use App\Http\Controllers\LogController;
Route::apiResource('logs', LogController::class);

use App\Http\Controllers\DocumentController;
Route::apiResource('documents', DocumentController::class);

use App\Models\Chat;
Route::get('chat-nlatest', function (Request $request) {
    $validated = $request->validate([
        'log_id' => 'nullable|string|max:36',
        'n' => 'nullable|integer',
    ]);
    $logId = $request->input('log_id');
    $n = $request->input('n', 10);
    $query = Chat::where('log_id', $logId);
    return $query->latest()->take($n)->select('role', 'content', 'created_at')->get();

});

use App\Http\Controllers\EmailController;
Route::post('send-email', [EmailController::class, 'sendEmail']);

use Illuminate\Support\Facades\Cache;
Route::middleware('api')->group(function () {

    Route::post('/service/create', function () {
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

    Route::post('/service/upload_url', function () {
        try {
            $response = Http::put('http://localhost:5000/upload_url', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to write to collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while writing to collection'], 500);
        }
    });

    Route::post('/service/upload_file', function () {
        try {
            $response = Http::put('http://localhost:5000/upload_file', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to write to collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while writing to collection'], 500);
        }
    });

    Route::post('/service/chat', function (Request $request) {
        try {

            $cacheKey = 'chat_' . md5(json_encode($request->only(['collection', 'query'])));
            if (Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }

            $response = Http::post('http://localhost:5000/chat', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to chat'], 500);
            }
            Cache::set($cacheKey, $response->json(), now()->addMinutes(10));
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while processing chat'], 500);
        }
    });

    Route::post('/service/clean', function () {
        try {
            $response = Http::delete('http://localhost:5000/clean', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to clear collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while clearing collection'], 500);
        }
    });

    Route::post('/service/delete', function () {
        try {
            $response = Http::delete('http://localhost:5000/delete', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to delete document'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while deleting document'], 500);
        }
    });

    Route::post('/service/show', function () {
        try {
            $response = Http::post('http://localhost:5000/show', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to show documents'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while showing documents'], 500);
        }
    });

});