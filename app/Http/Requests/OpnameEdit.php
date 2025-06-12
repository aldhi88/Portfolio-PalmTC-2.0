<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpnameEdit extends FormRequest
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
            'stock_in' => 'required|numeric|min:1',
            'stock_out' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'stock_in.required' => "The 'Stock In' field is required.",
            'stock_in.numeric' => "The 'Stock In' field is numeric.",
            'stock_out.required' => "The 'Stock Out' field is required.",
            'stock_out.numeric' => "The 'Stock Out' field is numeric.",
        ];
    }
}
