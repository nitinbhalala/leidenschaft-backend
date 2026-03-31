<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferTemplateRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'integer',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'nullable|image';
        } else {
            $rules['image'] = 'nullable|image';
        }

        return $rules;
    }
}
