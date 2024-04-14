<?php

namespace App\Http\Requests\Plugin;

use Illuminate\Foundation\Http\FormRequest;

class PluginLog extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'metadata' => 'nullable|json',
        ];
    }
}
