<?php

namespace App\Http\Requests\AircraftState;

use App\Models\Vatsim\GroundState;
use App\Rules\VatsimCallsign;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAircraftStates extends FormRequest
{
    public function rules(): array
    {
        return [
            'updates' => 'required|array',
            'updates.*.callsign' => ['required', 'string', new VatsimCallsign],
            'updates.*.clearance_flag' => 'sometimes|boolean',
            'updates.*.ground_state' => [
                'sometimes',
                'nullable',
                'string',
                Rule::enum(GroundState::class),
            ],
            'updates.*.clearance_flag_at' => 'sometimes|date',
            'updates.*.ground_state_at' => 'sometimes|date',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ((array) $this->input('updates', []) as $index => $update) {
                    if (! is_array($update)) {
                        continue;
                    }

                    $fields = array_filter(
                        ['clearance_flag', 'ground_state'],
                        fn (string $field): bool => array_key_exists($field, $update)
                    );

                    if ($fields === []) {
                        $validator->errors()->add(
                            'updates.'.$index,
                            'At least one of clearance_flag or ground_state must be provided.'
                        );
                    }

                    foreach ($fields as $field) {
                        if (! array_key_exists($field.'_at', $update)) {
                            $validator->errors()->add(
                                'updates.'.$index.'.'.$field.'_at',
                                'The '.$field.'_at field is required.'
                            );
                        }
                    }
                }
            },
        ];
    }
}
