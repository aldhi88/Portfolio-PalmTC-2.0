<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediumOpnameCreate extends FormRequest
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
            'stock_in' => 'numeric|min:0',
            'stock_out' => 'numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'stock_in.numeric' => "The 'Stock In' field is numeric.",
            'stock_out.numeric' => "The 'Stock Out' field is numeric.",
        ];
    }
}
