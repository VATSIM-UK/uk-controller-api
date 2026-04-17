<?php

namespace App\Http\Requests\Stand;

use App\Models\User\RoleKeys;
use App\Rules\Stand\StandReservationPlanPayload;
use Illuminate\Foundation\Http\FormRequest;

class StoreStandReservationPlan extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole(RoleKeys::VAA);
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email:rfc', 'max:255'],
            'payload' => ['required', 'array', new StandReservationPlanPayload()],
        ];
    }
}
