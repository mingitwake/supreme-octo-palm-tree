<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\Form;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\Question;

class FormController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Form::query();

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

    public function store(StoreFormRequest $request): JsonResponse
    {
        try {
            $form = Form::create($request->validated());
            return response()->json($form, 201);
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
            $form = Form::with(['questions.checkboxOptions','questions.tableColumns', 'questions.constraint'])->withTrashed()->findOrFail($id);
            if ($form->trashed()) {
                return response()->json([
                    'message' => 'Form Deleted',
                ], 404);
            }
            return response()->json($form, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Form not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateFormRequest $request, $id): JsonResponse
    {
        try {
            $form = Form::findOrFail($id);
            $form->update($request->validated());
            return response()->json($form, 200);
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
            $form = Form::findOrFail($id);
            $form->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Form not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

