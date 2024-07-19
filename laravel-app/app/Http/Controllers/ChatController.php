<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class ChatController extends Controller
// {
//     //
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $query = $request->input('query');

        // Make the HTTP request to the REST API
        $response = Http::post('http://localhost:5000/chat', [
            'query' => $query
        ]);

        // Return the response from the REST API
        return response()->json($response->json());
    }
}
