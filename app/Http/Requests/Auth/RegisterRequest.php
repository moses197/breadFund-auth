<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'firstname' => 'required|string|max:225',
            'lastname' => 'required|string|max:225',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|string|email|max:225|unique:users',
            'password' => 'required|string|min:4',
        ];
    }
}


/**
 * 'gender' => ''
 * 
 */