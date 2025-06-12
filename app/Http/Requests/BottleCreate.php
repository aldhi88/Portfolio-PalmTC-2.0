<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BottleCreate extends FormRequest
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
            'code' => 'required|unique:tc_bottles,code,NULL,id,deleted_at,NULL',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Bottle Code' field is required.",
            'code.unique' => "The 'Bottle Code' has already been taken.",
            'name.required' => "The 'Bottle Name' field is required.",
        ];
    }
}
