<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StandCreateRequest extends FormRequest
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
            'identifier' => 'required|string',
            'type_id' => 'required|exists:stand_types,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'terminal_id' => 'nullable|exists:terminals,id',
            'wake_category_id' => 'required|exists:wake_categories,id',
            'max_aircraft_id' => 'nullable|exists:aircraft,id',
            'assignment_priority' => 'nullable|integer|min:1' 
        ];
    }
}
