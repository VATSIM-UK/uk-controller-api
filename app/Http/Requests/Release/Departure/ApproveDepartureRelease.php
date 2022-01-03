<?php

namespace App\Http\Requests\Release\Departure;

use App\Rules\Controller\ControllerPositionValid;
use Illuminate\Foundation\Http\FormRequest;

class ApproveDepartureRelease extends FormRequest
{
    public function rules()
    {
        return [
            'expires_in_seconds' => 'present|nullable|integer|min:1',
            'released_at' => 'present|nullable|date_format:Y-m-d H:i:s',
            'controller_position_id' => [
                'required',
                'integer',
                new ControllerPositionValid(),
            ],
            'remarks' => [
                'string',
                'max:255',
            ],
        ];
    }
}
