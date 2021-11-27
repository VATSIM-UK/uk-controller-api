<?php

namespace App\Http\Requests\MissedApproach;

use Illuminate\Foundation\Http\FormRequest;

class AcknowledgeMissedApproach extends FormRequest
{
    public function rules(): array
    {
        return [
            'remarks' => [
                'required',
                'string'
            ],
        ];
    }
}
