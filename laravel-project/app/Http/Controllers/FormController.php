<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\Form;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = Form::all(); // You can change this to paginate in the future
        return response()->json($forms, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/forms

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
    // curl -i -X POST http://127.0.0.1:8080/api/forms -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $form = Form::findOrFail($id);
            return response()->json($form, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Form not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/forms/{id}

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
    // curl -i -X PUT http://127.0.0.1:8080/api/forms/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

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
    // curl -X DELETE http://127.0.0.1:8080/api/forms/{id}
}
