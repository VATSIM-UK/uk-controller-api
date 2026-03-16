<?php

namespace App\Services\JsonSchema;

use App\BaseUnitTestCase;

class StandReservationPlanSchemaValidatorTest extends BaseUnitTestCase
{
    public function testItRequiresReservationsOrStandSlotsViaAnyOf(): void
    {
        $validator = new StandReservationPlanSchemaValidator();

        $errors = $validator->validatePayload([
            'event_start' => '2026-03-01 09:00:00',
            'event_finish' => '2026-03-01 12:00:00',
        ]);

        $this->assertContains('$ must match at least one anyOf schema', $errors);
    }

    public function testItRequiresEventFinishForPayloadValidation(): void
    {
        $validator = new StandReservationPlanSchemaValidator();

        $errors = $validator->validatePayload([
            'event_start' => '2026-03-01 09:00:00',
            'reservations' => [
                [
                    'airport' => 'EGLL',
                    'stand' => '531',
                ],
            ],
        ]);

        $this->assertContains('$.event_finish is required', $errors);
    }
}
