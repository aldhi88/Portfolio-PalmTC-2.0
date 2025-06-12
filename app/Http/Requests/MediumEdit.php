<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediumEdit extends FormRequest
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
            'code' => 'required|unique:tc_mediums,code,'.$this->id,
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Medium Code' field is required.",
            'code.unique' => "The 'Medium Code' has already been taken.",
            'name.required' => "The 'Medium Name' field is required.",
        ];
    }
}
