<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlantationCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|unique:tc_plantations,code',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Plantation Code' field is required.",
            'code.unique' => "The 'Plantation Code' has already been taken.",
            'name.required' => "The 'Plantation Name' field is required.",
        ];
    }
}
