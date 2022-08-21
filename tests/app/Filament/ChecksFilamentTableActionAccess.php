<?php

namespace App\Filament;

use App\Models\User\RoleKeys;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

trait ChecksFilamentTableActionAccess
{
    public function testItShowsAndHidesTableActions(string $relationManagerClass, string $action, RoleKeys $role, bool $canSee)
    {
        Livewire::test()
            ->assertTableActionHidden()
        // X relation managers
        // Each with read only actions and write only actions
    }

    public function tableActionProvider(): array
    {
        
    }

    protected abstract function tableActionRecord(): Model;

    protected abstract function writeTableActions(): array;

    protected abstract function readOnlyTableActions(): array;
}
