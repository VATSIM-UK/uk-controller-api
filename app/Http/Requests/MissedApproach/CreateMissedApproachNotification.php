<?php

namespace App\Http\Requests\MissedApproach;

use Illuminate\Foundation\Http\FormRequest;

class CreateMissedApproachNotification extends FormRequest
{
    public function rules(): array
    {
        return [
            'callsign' => [
                'required',
                'string'
            ],
        ];
    }
}
