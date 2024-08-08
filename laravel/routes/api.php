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
Route::get('chat_pages', function (Request $request) {
    $query = Chat::query();
    
    if ($logId = $request->input('log_id')) {
        $query->where('log_id', $logId);
    }
    
    if ($role = $request->input('role')) {
        $query->where('role', $role);
    }

    if ($status = $request->input('status')) {
        $query->where('status', $status);
    }

    if ($sortBy = $request->input('sort_by')) {
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->paginate(20);
});

Route::get('chat_latest_n_records', function (Request $request) {
    
    $logId = $request->input('log_id');
    $n = $request->input('n', 10);
    $query = Chat::where('log_id', $logId);
    return $query->latest()->take($n)->select('role', 'content', 'created_at')->get();

});

use App\Models\Document;
Route::get('document_pages', function (Request $request) {
    $query = Document::query();
    
    if ($search = $request->input('search')) {
        $query->where('alias', 'LIKE', '%' . $search . '%');
    }
    
    if ($sortBy = $request->input('sort_by')) {
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->paginate(20);
});

use App\Http\Controllers\EmailController;
Route::post('send-email', [EmailController::class, 'sendEmail']);


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