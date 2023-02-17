<?php

namespace App\Policies;

use App\BaseFunctionalTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

class DefaultFilamentPolicyTest extends BaseFunctionalTestCase
{
    private readonly DefaultFilamentPolicy $defaultFilamentPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->defaultFilamentPolicy = $this->app->make(DefaultFilamentPolicy::class);
    }

    #[DataProvider('dataProvider')]
    public function testItManagesAccess(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->defaultFilamentPolicy->$action($user));
    }

    public static function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, true],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, true],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
            'View Any No Role' => ['viewAny', null, true],
            'View Any Operations' => ['viewAny', RoleKeys::OPERATIONS_TEAM, true],
            'View Any Web' => ['viewAny', RoleKeys::WEB_TEAM, true],
            'View Any DSG' => ['viewAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Update No Role' => ['update', null, false],
            'Update Operations' => ['update', RoleKeys::OPERATIONS_TEAM, true],
            'Update Web' => ['update', RoleKeys::WEB_TEAM, true],
            'Update DSG' => ['update', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Create No Role' => ['create', null, false],
            'Create Operations' => ['create', RoleKeys::OPERATIONS_TEAM, true],
            'Create Web' => ['create', RoleKeys::WEB_TEAM, true],
            'Create DSG' => ['create', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Delete No Role' => ['delete', null, false],
            'Delete Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, true],
            'Delete Web' => ['delete', RoleKeys::WEB_TEAM, true],
            'Delete DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Delete Any No Role' => ['deleteAny', null, false],
            'Delete Any Operations' => ['deleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Any Web' => ['deleteAny', RoleKeys::WEB_TEAM, false],
            'Delete Any DSG' => ['deleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore No Role' => ['restore', null, false],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, true],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, true],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Restore Any No Role' => ['restoreAny', null, false],
            'Restore Any Operations' => ['restoreAny', RoleKeys::OPERATIONS_TEAM, true],
            'Restore Any Web' => ['restoreAny', RoleKeys::WEB_TEAM, true],
            'Restore Any DSG' => ['restoreAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, true],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, true],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Force Delete Any No Role' => ['forceDeleteAny', null, false],
            'Force Delete Any Operations' => ['forceDeleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Any Web' => ['forceDeleteAny', RoleKeys::WEB_TEAM, false],
            'Force Delete Any DSG' => ['forceDeleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Dissociate No Role' => ['dissociate', null, false],
            'Dissociate Operations' => ['dissociate', RoleKeys::OPERATIONS_TEAM, true],
            'Dissociate Any Web' => ['dissociate', RoleKeys::WEB_TEAM, true],
            'Dissociate DSG' => ['dissociate', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Dissociate Any No Role' => ['dissociateAny', null, false],
            'Dissociate Any Operations' => ['dissociateAny', RoleKeys::OPERATIONS_TEAM, true],
            'Dissociate Any Any Web' => ['dissociateAny', RoleKeys::WEB_TEAM, true],
            'Dissociate Any DSG' => ['dissociateAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Replicate No Role' => ['replicate', null, false],
            'Replicate Operations' => ['replicate', RoleKeys::OPERATIONS_TEAM, true],
            'Replicate Any Web' => ['replicate', RoleKeys::WEB_TEAM, true],
            'Replicate DSG' => ['replicate', RoleKeys::DIVISION_STAFF_GROUP, true],

            // Filament Special Roles
            'Attach No Role' => ['attach', null, false],
            'Attach Operations' => ['attach', RoleKeys::OPERATIONS_TEAM, true],
            'Attach Web' => ['attach', RoleKeys::WEB_TEAM, true],
            'Attach DSG' => ['attach', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Detach No Role' => ['detach', null, false],
            'Detach Operations' => ['detach', RoleKeys::OPERATIONS_TEAM, true],
            'Detach Web' => ['detach', RoleKeys::WEB_TEAM, true],
            'Detach DSG' => ['detach', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Detach Any No Role' => ['detachAny', null, false],
            'Detach Any Operations' => ['detachAny', RoleKeys::OPERATIONS_TEAM, true],
            'Detach Any Web' => ['detachAny', RoleKeys::WEB_TEAM, true],
            'Detach Anh DSG' => ['detachAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Move Up No Role' => ['moveUp', null, false],
            'Move Up Operations' => ['moveUp', RoleKeys::OPERATIONS_TEAM, true],
            'Move Up Web' => ['moveUp', RoleKeys::WEB_TEAM, true],
            'Move Up DSG' => ['moveUp', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Move Down No Role' => ['moveDown', null, false],
            'Move Down Operations' => ['moveDown', RoleKeys::OPERATIONS_TEAM, true],
            'Move Down Web' => ['moveDown', RoleKeys::WEB_TEAM, true],
            'Move Down DSG' => ['moveDown', RoleKeys::DIVISION_STAFF_GROUP, true],
        ];
    }
}
