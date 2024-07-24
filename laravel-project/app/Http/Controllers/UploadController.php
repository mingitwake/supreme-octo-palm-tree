<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class UploadController extends Controller
// {
//     //
// }

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $document = $request->input('document');

        // Make the HTTP request to the Flask API
        $response = Http::post('http://localhost:5000/upload', [
            'document' => $document
        ]);

        // Return the response from the Flask API
        return response()->json($response->json());
    }
}
