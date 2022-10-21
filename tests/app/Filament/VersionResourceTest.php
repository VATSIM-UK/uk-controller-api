<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\VersionResource;
use App\Filament\Resources\VersionResource\Pages\ListVersions;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use App\Models\Version\Version;
use Filament\Tables\Actions\DeleteAction;
use Livewire\Livewire;

class VersionResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;

    protected function getIndexText(): array
    {
        return ['Versions', '2.0.1', 'Stable', 'Mon 04 Dec 2017, 00:00:00'];
    }

    protected function resourceClass(): string
    {
        return VersionResource::class;
    }

    /**
     * @dataProvider displaysActionProvider
     */
    public function testItDisplaysDeleteActionOnActiveVersions(?RoleKeys $role, bool $shouldDisplay)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $method = $shouldDisplay ? 'assertTableActionVisible' : 'assertTableActionHidden';
        Livewire::test(ListVersions::class, ['record' => 3])
            ->$method(
                'delete',
                Version::withTrashed()->findOrFail(3)
            );
    }

    /**
     * @dataProvider displaysActionProvider
     */
    public function testItDoesntDisplayRestoreActionOnActiveVersions(?RoleKeys $role)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        Livewire::test(ListVersions::class, ['record' => 3])
            ->assertTableActionHidden(
                'restore',
                Version::withTrashed()->findOrFail(3)
            );
    }

    /**
     * @dataProvider displaysActionProvider
     */
    public function testItDisplaysRestoreActionOnDeletedVersions(?RoleKeys $role, bool $shouldDisplay)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $method = $shouldDisplay ? 'assertTableActionVisible' : 'assertTableActionHidden';
        Livewire::test(ListVersions::class, ['record' => 1])
            ->$method(
                'restore',
                Version::withTrashed()->findOrFail(1)
            );
    }

    /**
     * @dataProvider displaysActionProvider
     */
    public function testItDoesntDisplayDeleteActionOnDeletedVersions(?RoleKeys $role)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        Livewire::test(ListVersions::class, ['record' => 1])
            ->assertTableActionHidden(
                'delete',
                Version::withTrashed()->findOrFail(1)
            );
    }

    public function displaysActionProvider(): array
    {
        return [
            'None' => [null, false],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, false],
            'Web' => [RoleKeys::WEB_TEAM, true],
        ];
    }
}
