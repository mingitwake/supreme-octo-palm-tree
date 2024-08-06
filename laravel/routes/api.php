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
Route::get('chatspage', function (Request $request) {
    $query = Chat::query();
    
    if ($search = $request->input('search')) {
        $query->where('log_id', 'LIKE', '%' . $search . '%');
    }
    
    if ($sortBy = $request->input('sort_by')) {
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->paginate(20);
});

use App\Models\Log;
Route::get('logspage', function (Request $request) {
    $query = Log::query();
    
    if ($search = $request->input('search')) {
        $query->where('title', 'LIKE', '%' . $search . '%');
    }
    
    if ($sortBy = $request->input('sort_by')) {
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->paginate(20);
});

use App\Models\Document;
Route::get('documentspage', function (Request $request) {
    $query = Document::query();
    
    if ($search = $request->input('search')) {
        $query->where('title', 'LIKE', '%' . $search . '%');
    }
    
    if ($sortBy = $request->input('sort_by')) {
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->paginate(20);
});

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

    Route::put('/service/upload', function () {
        try {
            $response = Http::put('http://localhost:5000/upload', request());
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
                // return response()->json(['error' => $response->body()], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while processing chat'], 500);
        }
    });

    Route::post('/service/clear', function () {
        try {
            $response = Http::delete('http://localhost:5000/clear', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to clear collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while clearing collection'], 500);
        }
    });

});