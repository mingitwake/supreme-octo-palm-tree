<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LogController;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Chat;

// Route::post('register', function (RegisterRequest $request) {
//     $user = User::create($request->validated());

//     // $token = $user->createToken('authToken')->accessToken;

//     return response()->json([
//         'user' => $user,
//         // 'access_token' => $token
//     ], 201);
// });
// Route::post('login', function (LoginRequest $request) {
//     if (Auth::attempt($request->validated())) {
//         $user = Auth::user();
//         $token = $user->createToken('authToken')->accessToken;

//         return response()->json([
//             'user' => $user,
//             'access_token' => $token
//         ], 200);
//     } else {
//         return response()->json([
//             'error' => 'Unauthorized'
//         ], 401);
//     }
// });

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
                // return response()->json($response->body(), 500);
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
                // return response()->json(['error' => 'Failed to chat'], 500);
                return response()->json($response->body(), 500);
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