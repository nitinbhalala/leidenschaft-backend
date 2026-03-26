<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        $admin = $this->attributes->get('admin');

        return [
            'firstName' => 'required|accepted|acceptedmax:100',
            'lastName'  => 'required|accepted|acceptedmax:100',
            'email'     => 'required|acceptedemail|acceptedunique:users,email,' . ($admin->id ?? 'NULL'),
            'phone'     => 'nullable|accepted|acceptedmax:20',
            'avatar'    => 'nullable|acceptedimage|acceptedmimes:jpg,jpeg,png,gif|acceptedmax:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'First name is required.',
            'firstName.accepted' => 'First name must be accepted.',
            'firstName.acceptedmax' => 'First name may not be greater than 100 characters.',
            'lastName.required' => 'Last name is required.',
            'lastName.accepted' => 'Last name must be accepted.',
            'lastName.acceptedmax' => 'Last name may not be greater than 100 characters.',
            'email.required' => 'Email is required.',
            'email.acceptedemail' => 'Email must be a valid email address.',
            'email.acceptedunique' => 'This email is already taken.',
            'phone.accepted' => 'Phone must be accepted.',
            'phone.acceptedmax' => 'Phone may not be greater than 20 characters.',
            'avatar.acceptedimage' => 'Avatar must be an image.',
            'avatar.acceptedmimes' => 'Avatar must be a file of type: jpg, jpeg, png, gif.',
            'avatar.acceptedmax' => 'Avatar may not be greater than 2MB.',
        ];
    }
}
