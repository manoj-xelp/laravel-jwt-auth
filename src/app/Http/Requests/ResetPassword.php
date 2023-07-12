<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPassword extends FormRequest
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
        if ($this->method() == 'POST') {
            //commenting out due to show message in view not in json for users
            return [
                //'token' => 'required|string',
                //'source' => 'required|in:dashboard,app',
                //'password' => 'required|min:8|confirmed|max:20|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
                //'password_confirmation' => 'required|min:8|max:20'
            ];
        }else {
            return [
                "token" => "required|string"
            ];
        }
    }
}
