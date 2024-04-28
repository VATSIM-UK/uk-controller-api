<?php

namespace App\Http\Requests\User;

use App\Rules\User\VatsimCid;
use Illuminate\Foundation\Http\FormRequest;

class CreateUser extends FormRequest
{
    public function rules()
    {
        return [
            'cid' => [
                'required',
                new VatsimCid(),
            ],
        ];
    }
}
