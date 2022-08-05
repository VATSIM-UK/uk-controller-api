<?php

namespace App\Filament\Widgets;

use App\BaseFilamentTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use Livewire\Livewire;

class MyRolesTest extends BaseFilamentTestCase
{
    /**
     * @dataProvider roleProvider
     */
    public function testItDisplaysRoles(array $roles)
    {
        $roleModels = Role::whereIn('key', $roles)->get();

        $this->assertEquals(count($roles), $roleModels->count());
        $this->filamentUser()->roles()->sync($roleModels->map(fn(Role $role): int => $role->id)->toArray());

        Livewire::test(MyRoles::class)
            ->assertSee($roleModels->map(fn(Role $role): string => $role->description)->toArray());
    }

    public function roleProvider(): array
    {
        return [
            'DSG' => [[RoleKeys::DIVISION_STAFF_GROUP]],
            'Web' => [[RoleKeys::WEB_TEAM]],
            'Ops' => [[RoleKeys::OPERATIONS_TEAM]],
            'Multiple' => [[RoleKeys::OPERATIONS_TEAM, RoleKeys::DIVISION_STAFF_GROUP]],
        ];
    }

    public function testItDisplaysNoRoles()
    {
        $this->filamentUser()->roles()->sync([]);

        Livewire::test(MyRoles::class)
            ->assertSee('Member');
    }
}
