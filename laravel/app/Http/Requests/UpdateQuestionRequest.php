<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
            'description'=>'nullable|string', 
            'content'=>'nullable|string', 
            'remarks'=>'nullable|string', 
            'no'=>'nullable|integer', 
            'required'=>'nullable|integer',
            'minselect'=>'nullable|integer', 
            'maxselect'=>'nullable|integer', 
            'others'=>'nullable|integer',
            'minlength'=>'nullable|integer', 
            'maxlength'=>'nullable|integer',
            'minvalue'=>'nullable|integer', 
            'maxvalue'=>'nullable|integer', 
            'decimalplace'=>'nullable|integer',
            'minrow'=>'nullable|integer', 
            'maxrow'=>'nullable|integer',
        ];
    }
}
