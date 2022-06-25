<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\StandResource;
use App\Models\User\User;

class StandResourceTest extends BaseFilamentTestCase
{
    public function testItIsForbiddenIfNotAdminUser()
    {
        $this->actingAs(User::factory()->create());
        $this->get(StandResource::getUrl())
            ->assertForbidden();
    }

    public function testItRendersTheIndexPage()
    {
        $this->get(StandResource::getUrl())
            ->assertSuccessful()
            ->assertSeeText('EGLL')
            ->assertSeeText('New stand');
    }
}
