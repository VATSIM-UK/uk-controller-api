<?php

use App\BaseFilamentTestCase;
use App\Filament\Resources\StandResource;

use App\Models\User\User;

use function Pest\Livewire\livewire;

it('is forbidden if not admin user ', function() {
    /** @var BaseFilamentTestCase $this */
    $this->actingAs(User::factory()->create());
    $this->get(StandResource::getUrl())
        ->assertForbidden();
});

it('renders the index page ', function() {
    /** @var BaseFilamentTestCase $this */
    $this->get(StandResource::getUrl())
        ->assertSuccessful()
        ->assertSeeText('EGLL')
        ->assertSeeText('New stand');
});
