<?php

namespace App\Http\Requests\Release\Departure;

use App\Models\Controller\ControllerPosition;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RequestDepartureRelease extends FormRequest
{
    public function rules()
    {
        return [
            'callsign' => [
                'required',
                'string',
            ],
            'requesting_controller_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!ControllerPosition::where('id', $value)->canRequestDepartureReleases()->exists()) {
                        $fail(sprintf('Controller position %d cannot request departure releases', $value));
                    }
                },
                'not_in:target_controller_id',
            ],
            'target_controller_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!ControllerPosition::where('id', $value)->canReceiveDepartureReleases()->exists()) {
                        $fail(sprintf('Controller position %d cannot receive departure releases', $value));
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

                $activeRequest = DepartureReleaseRequest::activeFor($validated['callsign'])
                    ->target($validated['target_controller_id'])
                    ->exists();

                if ($activeRequest) {
                    $validator->errors()->add(
                        'callsign',
                        sprintf(
                            'Cannot create release request for %s, one already active for target %d',
                            $validated['callsign'],
                            $validated['target_controller_id']
                        )
                    );
                }
            }
        );
    }
}
