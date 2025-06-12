<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallusTransferCreate extends FormRequest
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
            'bottle_used' => 'required|numeric|min:1',
            'new_bottle' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'bottle_used.required' => "The 'Workered Bottle' field is required.",
            'new_bottle.required' => "The 'New Bottle' field is required.",
            'bottle_used.numeric' => "The 'New Bottle' field is numeric.",
            'new_bottle.numeric' => "The 'New Bottle' field is numeric.",
        ];
    }
}
