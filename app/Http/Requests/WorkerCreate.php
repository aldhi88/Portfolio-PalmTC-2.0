<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkerCreate extends FormRequest
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
            'code' => 'required|unique:tc_workers,code,NULL,id,deleted_at,NULL',
            'name' => 'required',
            'no_pekerja' => 'required|numeric|unique:tc_workers,no_pekerja,NULL,id,deleted_at,NULL',
            'date_of_birth' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => "The 'Worker Code' field is required.",
            'code.unique' => "The 'Worker Code' has already been taken.",
            'name.required' => "The 'Worker Name' field is required.",
            'name.required' => "The 'Date of Birth' field is required.",
            'no_pekerja.required' => "The 'No.Pekerja' field is required.",
            'no_pekerja.unique' => "The 'No.Pekerja' has already been taken.",
        ];
    }
}
