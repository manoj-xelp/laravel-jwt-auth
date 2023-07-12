<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFile extends FormRequest
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
        /*since this request requires file upload Global headers will be overridden
         as application/json ⇢ multipart/form-data on request doc, which will not result in not 
         validate the request and shows the index page in response.
         Solution is to send Accept: application/json in header.
         */
        return [
            "type" => 'required|string|in:USER_PROFILE',
            "file" => 'required|file|mimes:png,jpg,jpeg,webp,pdf,mp4'
        ];
    }
}
