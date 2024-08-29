<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

// curl -i -X GET http://127.0.0.1/api/documents
// curl -i -X POST http://127.0.0.1/api/documents -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/documents/{id}
// curl -i -X PUT http://127.0.0.1/api/documents/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/documents/{id}

class DocumentController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Document::query();

        $validated = $request->validate([
            'search_by' => 'nullable|string',
            'search_value' => 'nullable|string',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);
    
        if ($request->search_by && $request->search_value) {
            $searchBy = $request->search_by;
            $searchValue = $request->search_value;
            $query->where($searchBy, 'LIKE', '%' . $searchValue . '%');
        }
        
        if ($request->sort_by && $request->sort_order) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    
        return $query->get();
        // return $query->paginate(10);
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
    
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->storeAs(
                    'uploads', $request->file('file')->getClientOriginalName()
                );
                $validated['file'] = $filePath;
                $document = Document::create($validated);
                return response()->json($document, 202);
            }

            if ($request->has('url')) {
                $url = $request->input('url');
                $document = Document::create($validated);
                return response()->json($document, 202);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([], 500);
    }
    
    public function show($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            return response()->json($document, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Document not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateDocumentRequest $request, $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $document->update($request->validated());
            return response()->json($document, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            Storage::delete($document->file);
            $document->delete();
            return response()->json(["id" => $id], 202);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Document not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}