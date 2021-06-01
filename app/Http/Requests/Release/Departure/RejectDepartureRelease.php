<?php

namespace App\Http\Requests\Release\Departure;

use App\Rules\Controller\ControllerPositionValid;
use Illuminate\Foundation\Http\FormRequest;

class RejectDepartureRelease extends FormRequest
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
