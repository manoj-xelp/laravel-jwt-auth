<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCities extends FormRequest
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
            "key" => "string|nullable|min:1",
            "country_id" => "integer|nullable|exists:countries,id",
            "state_id" => "integer|nullable|exists:states,id",
            "status" => "string|required|in:all,active,added"
        ];
    }
}
