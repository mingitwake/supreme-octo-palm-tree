<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\SelectedCheckbox;
use App\Models\TableRow;

// curl -i -X GET http://127.0.0.1/api/answers
// curl -i -X POST http://127.0.0.1/api/answers -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/answers/{id}
// curl -i -X PUT http://127.0.0.1/api/answers/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/answers/{id}

class AnswerController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Answer::query();

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

    public function store(StoreAnswerRequest $request): JsonResponse
    {
        try {
            $answer = Answer::create($request->validated());
            $type = Question::findOrFail($request->question_id)->type;
            if ($type) {
                switch ($type) {
                    case 'text':
                        break;
                    case 'table':
                        $tableRow = new TableRow([
                            'answer_id' => $answer->id, 
                            'contents' => $request->remarks,
                        ]);
                        $tableRow->save();
                        break;
                    case 'checkbox':
                        $selectedCheckbox = new SelectedCheckbox([
                            'answer_id' => $answer->id, 
                            'checkbox_option_id' => $request->checkbox_option_id, 
                            'remarks' => $request->remarks,
                        ]);
                        $selectedCheckbox->save();
                        break;
                    case 'number':
                        break;
                    case 'url':
                        break;
                    case 'email':
                        break;
                    case 'tel':
                        break;
                    default:
                        return response()->json([
                            'message' => 'Invalid question type'
                        ], 400);
                }
            }
            return response()->json($answer, 201);
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
            $answer = Answer::with(['question', 'tableRows', 'selectedCheckboxes'])->withTrashed()->findOrFail($id);
            if ($answer->trashed()) {
                return response()->json([
                    'message' => 'Answer Deleted',
                ], 404);
            }
            return response()->json($answer, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Answer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateAnswerRequest $request, $id): JsonResponse
    {
        try {
            $answer = Answer::findOrFail($id);
            $answer->update($request->validated());
            return response()->json($answer, 200);
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
            $answer = Answer::findOrFail($id);
            $answer->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Answer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
