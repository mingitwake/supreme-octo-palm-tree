<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\NumberConstraint;
use App\Models\CheckboxConstraint;
use App\Models\TextConstraint;
use App\Models\TableConstraint;

// curl -i -X GET http://127.0.0.1/api/questions
// curl -i -X POST http://127.0.0.1/api/questions -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/questions/{id}
// curl -i -X PUT http://127.0.0.1/api/questions/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/questions/{id}

class QuestionController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Question::query();

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

    public function store(StoreQuestionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $question = Question::create($validated);
            $constraint = null;
            if ($request->type) {
                switch ($request->type) {
                    case 'text':
                        $constraint = new TextConstraint([
                            'question_id'=>$question->id,
                            'minlength'=>$question->minselect, 
                            'maxlength'=>$question->maxselect, 
                        ]);
                        break;
                    case 'checkbox':
                        $constraint = new CheckboxConstraint([
                            'question_id'=>$question->id,
                            'minselect'=>$question->minselect, 
                            'maxselect'=>$question->maxselect, 
                            'others'=>$question->others,
                        ]);
                        break;
                    case 'number':
                        $constraint = new NumberConstraint([
                            'question_id'=>$question->id,
                            'minvalue'=>$question->minselect, 
                            'maxvalue'=>$question->maxselect, 
                            'decimalplace'=>$question->decimalplace,
                        ]);
                        break;
                    case 'table':
                        $constraint = new TableConstraint([
                            'question_id'=>$question->id,
                            'minrow'=>$question->minselect, 
                            'maxrow'=>$question->maxselect, 
                        ]);
                        break;
                    case 'url':
                        break;
                    case 'tel':
                        break;
                    default:
                        break;
                }
                if ($constraint) {
                    $constraint->save();
                    $question->constraint()->associate($constraint);
                    $question->save();
                }
            }
            return response()->json($question, 201);
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
            $question = Question::with(['checkboxOptions', 'tableColumns', 'constraint'])
            ->withTrashed()->findOrFail($id);
            if ($question->trashed()) {
                return response()->json([
                    'message' => 'Question Deleted',
                ], 404);
            }
            return response()->json($question, 200);        
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Question not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateQuestionRequest $request, $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            if ($question->constraint) {
                $constraint = $question->constraint;
                $constraint->update($request->validated());
            }
            $question->update($request->validated());
            return response()->json($question, 200);
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
            $question = Question::findOrFail($id);
            $question->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Question not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

