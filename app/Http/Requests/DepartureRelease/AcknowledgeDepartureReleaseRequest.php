<?php

namespace App\Http\Requests\DepartureRelease;

use App\Rules\Controller\ControllerPositionValid;
use Illuminate\Foundation\Http\FormRequest;

class AcknowledgeDepartureReleaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'controller_position_id' => [
                'required',
                'integer',
                new ControllerPositionValid(),
            ],
        ];
    }
}
