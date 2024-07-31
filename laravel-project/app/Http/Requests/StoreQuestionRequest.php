<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            "form_id"=> "required|string|max:36",
            "content"=> "required|string|max:512",
            "type"=> "required|string",
            "remarks"=> "string|max:512",
            "status"=> "string|in:active,inactive",
        ];
    }
}