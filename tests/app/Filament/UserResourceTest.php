<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;
use App\Filament\Resources\UserResource;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use TestingUtils\Traits\WithSeedUsers;

class UserResourceTest extends BaseFilamentTestCase
{
    use WithSeedUsers;
    use ChecksListingFilamentAccess;
    use ChecksViewFilamentAccess;

    public static function indexRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }

    public static function viewRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }

    #[DataProvider('editRoleProvider')]
    public function testItCanBeEdited(?RoleKeys $role, bool $shouldBeAllowed, bool $sameUser)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $userToEdit = $sameUser
            ? $user
            : $this->activeUser();

        if ($shouldBeAllowed) {
            $this->get(UserResource::getUrl('view', ['record' => $userToEdit]))
                ->assertSuccessful();
        } else {
            $this->get(UserResource::getUrl('view', ['record' => $userToEdit]))
                ->assertForbidden();
        }
    }

    public static function editRoleProvider(): array
    {
        return [
            'None Different User' => [null, false, false],
            'None Same User' => [null, false, true],
            'Contributor Different User' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false, false],
            'Contributor Same User' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false, true],
            'DSG Different User' => [RoleKeys::DIVISION_STAFF_GROUP, true, true],
            'DSG Same User' => [RoleKeys::DIVISION_STAFF_GROUP, true, false],
            'Web Different User' => [RoleKeys::WEB_TEAM, true, true],
            'Web Same User' => [RoleKeys::WEB_TEAM, true, false],
            'Operations Same User' => [RoleKeys::OPERATIONS_TEAM, false, false],
            'Operations Different User' => [RoleKeys::OPERATIONS_TEAM, false, false],
        ];
    }

    public function testItLoadsDataForView()
    {
        Livewire::test(UserResource\Pages\ViewUser::class, ['record' => self::BANNED_USER_CID])
            ->assertSet('data.first_name', 'User')
            ->assertSet('data.last_name', 'Banned')
            ->assertSet('data.status', 2);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => self::BANNED_USER_CID])
            ->assertSet('data.first_name', 'User')
            ->assertSet('data.last_name', 'Banned')
            ->assertSet('data.status', 2);
    }

    public function testAUsersStatusCanBeChanged()
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => self::BANNED_USER_CID])
            ->set('data.status', 1)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(1, $this->activeUser()->status);
    }

    public function testItListsRoles()
    {
        $rowToExpect = Role::idFromKey(RoleKeys::DIVISION_STAFF_GROUP);
        $this->bannedUser()->roles()->sync([$rowToExpect]);

        Livewire::test(
            UserResource\RelationManagers\RolesRelationManager::class,
            ['ownerRecord' => $this->bannedUser(), 'pageClass' => EditUser::class]
        )
            ->assertCanSeeTableRecords([$rowToExpect]);
    }

    public function testRolesCanBeAdded()
    {
        $rowToExpect = Role::idFromKey(RoleKeys::DIVISION_STAFF_GROUP);

        Livewire::test(
            UserResource\RelationManagers\RolesRelationManager::class,
            ['ownerRecord' => $this->bannedUser(), 'pageClass' => EditUser::class]
        )
            ->callTableAction('attach', data: ['recordId' => $rowToExpect])
            ->assertHasNoTableActionErrors();

        $this->assertEquals([$rowToExpect], $this->bannedUser()->roles->pluck('id')->toArray());
    }

    public function testRolesCanBeRemoved()
    {
        $rowToExpect = Role::idFromKey(RoleKeys::DIVISION_STAFF_GROUP);
        $this->bannedUser()->roles()->sync([$rowToExpect]);

        Livewire::test(
            UserResource\RelationManagers\RolesRelationManager::class,
            ['ownerRecord' => $this->bannedUser(), 'pageClass' => EditUser::class]
        )
            ->callTableAction('detach', Role::fromKey(RoleKeys::DIVISION_STAFF_GROUP))
            ->assertHasNoTableActionErrors();

        $this->assertEmpty($this->bannedUser()->roles);
    }

    protected function getIndexText(): array
    {
        return ['Users'];
    }

    protected function getViewText(): string
    {
        return 'View User Banned';
    }

    protected function getViewRecord(): Model
    {
        return User::findOrFail(self::BANNED_USER_CID);
    }

    protected static function resourceClass(): string
    {
        return UserResource::class;
    }
}
