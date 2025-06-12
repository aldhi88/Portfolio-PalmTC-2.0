<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaminationCreate extends FormRequest
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
            'code' => 'required|unique:tc_contaminations,code',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Contamination Code' field is required.",
            'code.unique' => "The 'Contamination Code' has already been taken.",
            'name.required' => "The 'Contamination Name' field is required.",
        ];
    }
}
