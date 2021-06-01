<?php

namespace App\Http\Requests\Release\Departure;

use App\Models\Controller\ControllerPosition;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class RequestDepartureRelease extends FormRequest
{
    public function rules()
    {
        return [
            'callsign' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (DepartureReleaseRequest::activeFor($value)->exists()) {
                        $fail(sprintf('Cannot create release request for %s, one already active', $value));
                    }
                },
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
}
