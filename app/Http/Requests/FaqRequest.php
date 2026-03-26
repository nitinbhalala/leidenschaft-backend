<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Question is required.',
            'question.string' => 'Question must be a string.',
            'question.max' => 'Question may not be greater than 1000 characters.',
            'answer.required' => 'Answer is required.',
            'answer.string' => 'Answer must be a string.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 0 or 1.',
        ];
    }
}
