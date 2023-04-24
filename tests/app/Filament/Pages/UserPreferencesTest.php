<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use Livewire\Livewire;

class UserPreferencesTest extends BaseFilamentTestCase
{
    public function testItTogglesStandAcarsMessages()
    {
        Livewire::test(UserPreferences::class)
            ->set('send_stand_acars_messages', true);

        $this->assertDatabaseHas(
            'user',
            [
                'id' => self::ACTIVE_USER_CID,
                'send_stand_acars_messages' => 1,
            ]
        );

        Livewire::test(UserPreferences::class)
            ->set('send_stand_acars_messages', false);

        $this->assertDatabaseHas(
            'user',
            [
                'id' => self::ACTIVE_USER_CID,
                'send_stand_acars_messages' => 0,
            ]
        );
    }
}
