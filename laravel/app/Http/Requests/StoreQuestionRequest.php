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
            'form_id'=>'required|string|max:36', 
            'description'=>'nullable|string', 
            'content'=>'nullable|string', 
            'remarks'=>'nullable|string', 
            'no'=>'nullable|integer', 
            'required'=>'nullable|integer',
            'type'=>'required|string|in:text,tel,number,url,date,email,checkbox,table',
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
