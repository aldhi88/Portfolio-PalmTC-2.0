<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediumStockCreate extends FormRequest
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
            'stock' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'stock.required' => "The 'Stock' field is required.",
            'stock.numeric' => "The 'Stock' field is numeric.",
        ];
    }
}
