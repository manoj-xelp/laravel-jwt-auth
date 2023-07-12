<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,'.$this->logged_in_user['id'],
            'dial_code'=> 'nullable|string',
            'mobile_number' => 'nullable|numeric',
            'date_of_birth' => 'nullable|date',
            'about_me' => 'nullable|string|max:500',
            'profile_pic' => 'nullable|string|max:150'
        ];
    }
}
