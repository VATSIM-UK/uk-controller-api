<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'title' => 'required|string',
            'link' => 'required|url',
            'body' => 'required|string',
            'valid_from' => 'required|date|before:valid_to',
            'valid_to' => 'required|date|after:valid_from',
            'all_positions' => 'boolean',
            'positions' => 'required_if:all_positions,false|exclude_if:all_positions,true|array'
        ];
    }
}
