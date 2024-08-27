<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\NumberConstraintController;
use App\Http\Controllers\CheckboxOptionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TableColumnsController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\AnswerController;
use App\Http\Requests\ServiceChatRequest;
use App\Http\Requests\ServiceDeleteRequest;
use App\Http\Requests\ServiceUploadRequest;
use App\Models\Log;
use App\Models\Document;

Route::group(['middleware' => 'auth:api'], function () {
    // Route::get('user', function (Request $request) {
    //     return $request->user();
    // });

    // Route::post('logout', function (Request $request) {
    //     $request->user()->token()->revoke();
    //     return response()->json(['message' => 'Successfully logged out']);
    // });
});

Route::middleware(['api'])->group(function () {
    Route::apiResource('chats', ChatController::class);
    Route::apiResource('logs', LogController::class);
    Route::apiResource('documents', DocumentController::class);
    Route::post('send-email', [EmailController::class, 'sendEmail']);
    Route::apiResource('forms', FormController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('textconstraints', TextConstraintController::class);
    Route::apiResource('numberconstraints', NumberConstraintController::class);
    Route::apiResource('tableconstraints', TableConstraintController::class);
    Route::apiResource('checkboxconstraints', CheckboxConstraintController::class);
    Route::apiResource('checkboxoptions', CheckboxOptionController::class);
    Route::apiResource('responses', ResponseController::class);
    Route::apiResource('answers', AnswerController::class);
    Route::apiResource('tablecolumns', TableColumnsController::class);

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

    Route::post('/service/upload_url', function (ServiceUploadRequest $request) {
        try {
            $document = Document::create($request->validated());
            $request->merge(['id'=>$document->id]);
            $response = Http::put('http://localhost:5000/upload_url', request());
            if ($response->failed()) {
                $document->forceDelete();
                return response()->json(['error' => 'Failed to write to collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            if ($document) {
                $document->forceDelete();
            }
            return response()->json(['error' => 'Error occurred while writing to collection'], 500);
        }
    });

    Route::post('/service/upload_file', function (ServiceUploadRequest $request) {
        try {
            $document = Document::create($request->validated());
            $filePath = $request->file('file')->storeAs(
                'uploads', $request->file('file')->getClientOriginalName()
            );
            $request->merge(['id'=>$document->id, 'file'=>$filePath]);
            $response = Http::put('http://localhost:5000/upload_file', request());
            if ($response->failed()) {
                $document->forceDelete();
                return response()->json(['error' => 'Failed to write to collection'], 500);
            }
            return $response->json();
        } catch (\Exception $e) {
            if ($document) {
                $document->forceDelete();
            }
            return response()->json(['error' => 'Error occurred while writing to collection'], 500);
        }
    });

    Route::post('/service/chat', function (ServiceChatRequest $request) {
        try {

            $cacheKey = 'chat_' . md5(json_encode($request->only(['query', 'log_id'])));
            if (Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }

            if ($request->log_id) {
                $log = Log::with(['chats'])->findOrFail($request->log_id);
                $chats = json_encode($log->chats()->select('id', 'role', 'content', 'created_at')->orderBy('created_at', 'desc')->take(10)->get());
                $request->merge(['histories'=>substr($chats, 0, 1000)]);             
            }
            $response = Http::timeout(20)->post('http://localhost:5000/chat', request());
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to chat'], 500);
            }
            Cache::set($cacheKey, $response->json(), now()->addMinutes(60));
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

    Route::post('/service/delete', function (ServiceDeleteRequest $request) {
        try {
            $validated = $request->validated();
            $response = Http::delete('http://localhost:5000/delete', request());
            $document = Document::findOrFail($request->id);
            if ( $document->file ) {
                Storage::delete($document->file);
            }
            $document->delete();
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