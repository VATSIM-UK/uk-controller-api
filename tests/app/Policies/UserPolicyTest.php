<?php

namespace App\Policies;

use App\BaseFunctionalTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

class UserPolicyTest extends BaseFunctionalTestCase
{
    private readonly UserPolicy $userPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->userPolicy = $this->app->make(UserPolicy::class);
    }

    #[DataProvider('updateDataProvider')]
    public function testItManagesUpdateAccess(string $action, ?RoleKeys $role, bool $sameUser, bool $expected)
    {
        $user = User::factory()->create();
        $otherUser = $sameUser
            ? $user
            : User::factory()->create();

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->userPolicy->$action($user, $otherUser));
    }

    public static function updateDataProvider(): array
    {
        return [
            'Update No Role Different User' => ['update', null, false, false],
            'Update No Role Same User' => ['update', null, true, false],
            'Update Operations Contributor Different User' => ['update', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Update Operations Contributor Same User' => ['update', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Update DSG Different User' => ['update', RoleKeys::DIVISION_STAFF_GROUP, false, true],
            'Update DSG Same User' => ['update', RoleKeys::DIVISION_STAFF_GROUP, true, false],
            'Update Web Different User' => ['update', RoleKeys::WEB_TEAM, false, true],
            'Update Web Same User' => ['update', RoleKeys::WEB_TEAM, true, false],
            'Update Operations Different User' => ['update', RoleKeys::OPERATIONS_TEAM, false, false],
            'Update Operations Same User' => ['update', RoleKeys::OPERATIONS_TEAM, true, false],

            // Filament special roles
            'Attach No Role Different User' => ['attach', null, false, false],
            'Attach No Role Same User' => ['attach', null, true, false],
            'Attach Operations Contributor Different User' => ['attach', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Attach Operations Contributor Same User' => ['attach', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Attach DSG Different User' => ['attach', RoleKeys::DIVISION_STAFF_GROUP, false, true],
            'Attach DSG Same User' => ['attach', RoleKeys::DIVISION_STAFF_GROUP, true, false],
            'Attach Web Different User' => ['attach', RoleKeys::WEB_TEAM, false, true],
            'Attach Web Same User' => ['attach', RoleKeys::WEB_TEAM, true, false],
            'Attach Operations Different User' => ['attach', RoleKeys::OPERATIONS_TEAM, false, false],
            'Attach Operations Same User' => ['attach', RoleKeys::OPERATIONS_TEAM, true, false],
            'Detach No Role Different User' => ['detach', null, false, false],
            'Detach No Role Same User' => ['detach', null, true, false],
            'Detach Operations Contributor Different User' => ['detach', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Detach Operations Contributor Same User' => ['detach', RoleKeys::OPERATIONS_CONTRIBUTOR, true, false],
            'Detach DSG Different User' => ['detach', RoleKeys::DIVISION_STAFF_GROUP, false, true],
            'Detach DSG Same User' => ['detach', RoleKeys::DIVISION_STAFF_GROUP, true, false],
            'Detach Web Different User' => ['detach', RoleKeys::WEB_TEAM, false, true],
            'Detach Web Same User' => ['detach', RoleKeys::WEB_TEAM, true, false],
            'Detach Operations Different User' => ['detach', RoleKeys::OPERATIONS_TEAM, false, false],
            'Detach Operations Same User' => ['detach', RoleKeys::OPERATIONS_TEAM, true, false],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testItManagesAccessForOtherRoles(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->userPolicy->$action($user));
    }

    public static function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, false],
            'View Operations Contributor' => ['view', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, false],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
            'View Any No Role' => ['viewAny', null, false],
            'View Any Operations Contributor' => ['viewAny', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'View Any Operations' => ['viewAny', RoleKeys::OPERATIONS_TEAM, false],
            'View Any Web' => ['viewAny', RoleKeys::WEB_TEAM, true],
            'View Any DSG' => ['viewAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Create No Role' => ['create', null, false],
            'Create Any Operations Contributor' => ['create', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'Create Operations' => ['create', RoleKeys::OPERATIONS_TEAM, false],
            'Create Web' => ['create', RoleKeys::WEB_TEAM, false],
            'Create DSG' => ['create', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete No Role' => ['delete', null, false],
            'Delete Operations Contributor' => ['delete', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'Delete Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Web' => ['delete', RoleKeys::WEB_TEAM, false],
            'Delete DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete Any No Role' => ['deleteAny', null, false],
            'Delete Any Contributor' => ['deleteAny', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'Delete Any Operations' => ['deleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Any Web' => ['deleteAny', RoleKeys::WEB_TEAM, false],
            'Delete Aby DSG' => ['deleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore No Role' => ['restore', null, false],
            'Restore Contributor' => ['restore', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, false],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, false],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete  Contributor' => ['forceDelete', RoleKeys::OPERATIONS_CONTRIBUTOR,  false],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, false],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, false],
        ];
    }
}
