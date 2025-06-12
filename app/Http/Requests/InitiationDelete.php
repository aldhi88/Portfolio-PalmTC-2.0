<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiationDelete extends FormRequest
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
            'pass_confirm' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pass_confirm.required' => "The 'Confirmation Password' field is required.",
        ];
    }
}