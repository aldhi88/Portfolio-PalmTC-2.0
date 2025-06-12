<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkerEdit extends FormRequest
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
            'code' => 'required|unique:tc_workers,code,'.$this->id.',id,deleted_at,NULL',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Worker Code' field is required.",
            'code.unique' => "The 'Worker Code' has already been taken.",
            'name.required' => "The 'Worker Name' field is required.",
        ];
    }
}
