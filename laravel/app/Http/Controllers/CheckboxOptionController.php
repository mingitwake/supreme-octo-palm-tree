<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCheckboxOptionRequest;
use App\Http\Requests\UpdateCheckboxOptionRequest;
use App\Models\CheckboxOption;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CheckboxOptionController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = CheckboxOption::query();

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
    
        return $query->paginate(10);
    }

    public function store(StoreCheckboxOptionRequest $request): JsonResponse
    {
        try {
            $checkboxoption = CheckboxOption::create($request->validated());
            return response()->json($checkboxoption, 201);
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
    
    public function show($id): JsonResponse
    {
        try {
            $checkboxoption = CheckboxOption::findOrFail($id);
            return response()->json($checkboxoption, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'CheckboxOption not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateCheckboxOptionRequest $request, $id): JsonResponse
    {
        try {
            $checkboxoption = CheckboxOption::findOrFail($id);
            $checkboxoption->update($request->validated());
            return response()->json($checkboxoption, 200);
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
            $checkboxoption = CheckboxOption::findOrFail($id);
            $checkboxoption->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'CheckboxOption not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}


