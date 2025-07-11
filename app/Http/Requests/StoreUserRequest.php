<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string',
            'position_id' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'phone' => 'required|string',
            'passport' => 'required|string',
            'jshshir' => 'required|string|max:14',
        ];
    }
}
