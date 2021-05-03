<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class HoldRequest extends FormRequest
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
            'inbound_heading' => 'required|integer|gt:0|lte:360',
            'turn_direction' => [
                'required',
                Rule::in(['left', 'right'])
            ],
            'minimum_altitude' => 'required|integer|digits_between:4,5',
            'maximum_altitude' => 'required|integer|digits_between:4,5',
            'description' => 'nullable|string'
        ];
    }
}
