<?php

namespace App\Models\Acars;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class AcarsMessageTest extends BaseFunctionalTestCase
{
    public function testItPrunes()
    {
        // Should be deleted, far too old
        $message1 = AcarsMessage::create(
            [
                'message' => '1',
                'successful' => true,
            ]
        );
        $message1->created_at = Carbon::now()->subMonths(2);
        $message1->save();

        // Should be deleted, just too old
        $message2 = AcarsMessage::create(
            [
                'message' => '1',
                'successful' => true,
            ]
        );
        $message2->created_at = Carbon::now()->subMonth()->subMinute();
        $message2->save();

        // Should be kept, not quite old enough
        $message3 = AcarsMessage::create(
            [
                'message' => '1',
                'successful' => true,
            ]
        );
        $message3->created_at = Carbon::now()->subMonth()->addMinute();
        $message3->save();

        // Should be kept, very recent
        $message4 = AcarsMessage::create(
            [
                'message' => '1',
                'successful' => true,
            ]
        );

        // Prune
        $message1->prunable()->delete();
        $this->assertDatabaseCount('acars_messages', 2);
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'id' => $message3->id,
            ]
        );
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'id' => $message4->id,
            ]
        );
        $this->assertDatabaseMissing(
            'acars_messages',
            [
                'id' => $message2->id,
            ]
        );
        $this->assertDatabaseMissing(
            'acars_messages',
            [
                'id' => $message1->id,
            ]
        );
    }
}
