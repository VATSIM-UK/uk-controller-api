<?php

namespace App\Http\Requests\Prenote;

use App\Models\Controller\ControllerPosition;
use App\Models\Prenote\PrenoteMessage;
use App\Rules\Airfield\AirfieldIcao;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreatePrenoteMessage extends FormRequest
{
    public function rules()
    {
        return [
            'callsign' => [
                'required',
                'string',
            ],
            'departure_airfield' => [
                'required',
                'string',
                new AirfieldIcao(),
            ],
            'departure_sid' => [
                'present',
                'string',
                'nullable',
            ],
            'destination_airfield' => [
                'present',
                'string',
                'nullable',
                new AirfieldIcao(),
            ],
            'requesting_controller_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!ControllerPosition::where('id', $value)->canSendPrenotes()->exists()) {
                        $fail(sprintf('Controller position %d cannot send prenotes', $value));
                    }
                },
                'not_in:target_controller_id',
            ],
            'target_controller_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!ControllerPosition::where('id', $value)->canReceivePrenotes()->exists()) {
                        $fail(sprintf('Controller position %d cannot receive prenotes', $value));
                    }
                },
                'different:requesting_controller_id',
            ],
            'expires_in_seconds' => 'required|integer|min:1',
        ];
    }

    public function validator()
    {
        return $this->createDefaultValidator($this->container->make(ValidationFactory::class))->after(
            function (Validator $validator) {
                $validated = $validator->validated();
                if (!isset($validated['callsign'], $validated['target_controller_id'])) {
                    return;
                }

                $activeMessage = PrenoteMessage::activeFor($validated['callsign'])
                    ->target($validated['target_controller_id'])
                    ->exists();

                if ($activeMessage) {
                    $validator->errors()->add(
                        'callsign',
                        sprintf(
                            'Cannot create prenote message for %s, one already active for target %d',
                            $validated['callsign'],
                            $validated['target_controller_id']
                        )
                    );
                }
            }
        );
    }
}
