<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'response_id' => 'required|string|max:36', 
            'question_id' => 'required|string|max:36', 
            'content' => 'nullable|string', 
            'checkbox_option_id' => 'nullable|string|max:36', 
            'remarks' => 'nullable|string',
            'contents' => 'nullable|string', 
            'delimiter' => 'nullable|string', 
        ];
    }
}
