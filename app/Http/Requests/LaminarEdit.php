<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaminarEdit extends FormRequest
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
            'code' => 'required|unique:tc_laminars,code,'.$this->id,
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Laminar Code' field is required.",
            'code.unique' => "The 'Laminar Code' has already been taken.",
            'name.required' => "The 'Laminar Name' field is required.",
        ];
    }
}
