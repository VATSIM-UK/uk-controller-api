<?php

namespace App\Http\Requests\Prenote;

use App\Rules\Controller\ControllerPositionValid;
use Illuminate\Foundation\Http\FormRequest;

class AcknowledgePrenoteMessage extends FormRequest
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
