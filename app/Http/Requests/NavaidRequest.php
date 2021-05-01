<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class NavaidRequest extends FormRequest
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
        $uniqueRule = $this->method() == 'POST' ?
            Rule::unique('navaids', 'identifier') :
            Rule::unique('navaids', 'identifier')->ignore($this->identifier, 'identifier');

        return [
            'identifier' => ["required", "string", "max:5", $uniqueRule],
            'latitude' => ["required", "numeric"],
            'longitude' => ["required", "numeric"]
        ];
    }
}
