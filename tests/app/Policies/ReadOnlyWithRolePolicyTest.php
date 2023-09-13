<?php

namespace App\Policies;

use App\BaseUnitTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

class ReadOnlyWithRolePolicyTest extends BaseUnitTestCase
{
    private readonly ReadOnlyWithRolePolicy $policy;

    public function setUp(): void
    {
        parent::setUp();
        $this->policy = $this->app->make(ReadOnlyWithRolePolicy::class);
    }

    #[DataProvider('dataProvider')]
    public function testItManagesAccess(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->policy->$action($user));
    }

    public static function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, false],
            'View Operations Contributor' => ['view',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, true],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
            'View Any No Role' => ['viewAny', null, false],
            'View Any Operations Contributor' => ['viewAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'View Any Operations' => ['viewAny', RoleKeys::OPERATIONS_TEAM, true],
            'View Any Web' => ['viewAny', RoleKeys::WEB_TEAM, true],
            'View Any DSG' => ['viewAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Update No Role' => ['update', null, false],
            'Update Operations Contributor' => ['update',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Update Operations' => ['update', RoleKeys::OPERATIONS_TEAM, false],
            'Update Web' => ['update', RoleKeys::WEB_TEAM, false],
            'Update DSG' => ['update', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Create No Role' => ['create', null, false],
            'Create Operations Contributor' => ['create',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Create Operations' => ['create', RoleKeys::OPERATIONS_TEAM, false],
            'Create Web' => ['create', RoleKeys::WEB_TEAM, false],
            'Create DSG' => ['create', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete No Role' => ['delete', null, false],
            'Delete Operations Contributor' => ['delete',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Delete Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Web' => ['delete', RoleKeys::WEB_TEAM, false],
            'Delete DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete Any No Role' => ['deleteAny', null, false],
            'Delete Any Operations Contributor' => ['deleteAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Delete Any Operations' => ['deleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Any Web' => ['deleteAny', RoleKeys::WEB_TEAM, false],
            'Delete Any DSG' => ['deleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore No Role' => ['restore', null, false],
            'Restore Operations Contributor' => ['restore',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, false],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, false],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore Any No Role' => ['restoreAny', null, false],
            'Restore Any Operations Contributor' => ['restoreAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Restore Any Operations' => ['restoreAny', RoleKeys::OPERATIONS_TEAM, false],
            'Restore Any Web' => ['restoreAny', RoleKeys::WEB_TEAM, false],
            'Restore Any DSG' => ['restoreAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete Operations Contributor' => ['forceDelete',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, false],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Force Delete Any No Role' => ['forceDeleteAny', null, false],
            'Force Delete Any Operations Contributor' => ['forceDeleteAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Force Delete Any Operations' => ['forceDeleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Any Web' => ['forceDeleteAny', RoleKeys::WEB_TEAM, false],
            'Force Delete Any DSG' => ['forceDeleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Dissociate No Role' => ['dissociate', null, false],
            'Dissociate Operations Contributor' => ['dissociate',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Dissociate Operations' => ['dissociate', RoleKeys::OPERATIONS_TEAM, false],
            'Dissociate Any Web' => ['dissociate', RoleKeys::WEB_TEAM, false],
            'Dissociate DSG' => ['dissociate', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Dissociate Any No Role' => ['dissociateAny', null, false],
            'Dissociate Any Operations Contributor' => ['dissociateAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Dissociate Any Operations' => ['dissociateAny', RoleKeys::OPERATIONS_TEAM, false],
            'Dissociate Any Any Web' => ['dissociateAny', RoleKeys::WEB_TEAM, false],
            'Dissociate Any DSG' => ['dissociateAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Replicate No Role' => ['replicate', null, false],
            'Replicate Operations Contributor' => ['replicate',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Replicate Operations' => ['replicate', RoleKeys::OPERATIONS_TEAM, false],
            'Replicate Any Web' => ['replicate', RoleKeys::WEB_TEAM, false],
            'Replicate DSG' => ['replicate', RoleKeys::DIVISION_STAFF_GROUP, false],

            // Filament Special Roles
            'Attach No Role' => ['attach', null, false],
            'Attach Operations Contributor' => ['attach',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Attach Operations' => ['attach', RoleKeys::OPERATIONS_TEAM, false],
            'Attach Web' => ['attach', RoleKeys::WEB_TEAM, false],
            'Attach DSG' => ['attach', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Detach No Role' => ['detach', null, false],
            'Detach Operations Contributor' => ['detach',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Detach Operations' => ['detach', RoleKeys::OPERATIONS_TEAM, false],
            'Detach Web' => ['detach', RoleKeys::WEB_TEAM, false],
            'Detach DSG' => ['detach', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Detach Any No Role' => ['detachAny', null, false],
            'Detach Any Operations Contributor' => ['detachAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Detach Any Operations' => ['detachAny', RoleKeys::OPERATIONS_TEAM, false],
            'Detach Any Web' => ['detachAny', RoleKeys::WEB_TEAM, false],
            'Detach Anh DSG' => ['detachAny', RoleKeys::DIVISION_STAFF_GROUP, false],
        ];
    }
}
