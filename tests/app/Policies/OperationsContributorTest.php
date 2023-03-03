<?php

namespace App\Policies;

use App\BaseFunctionalTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

class OperationsContributorTest extends BaseFunctionalTestCase
{
    private readonly OperationsContributorPolicy $opsContributorFilamentPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->opsContributorFilamentPolicy = $this->app->make(OperationsContributorPolicy::class);
    }

    #[DataProvider('dataProvider')]
    public function testItManagesAccess(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->opsContributorFilamentPolicy->$action($user));
    }

    public static function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, true],
            'View Operations Contributor' => ['view',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, true],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
            'View Any No Role' => ['viewAny', null, true],
            'View Any Operations Contributor' => ['viewAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'View Any Operations' => ['viewAny', RoleKeys::OPERATIONS_TEAM, true],
            'View Any Web' => ['viewAny', RoleKeys::WEB_TEAM, true],
            'View Any DSG' => ['viewAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Update No Role' => ['update', null, false],
            'Update Operations Contributor' => ['update',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Update Operations' => ['update', RoleKeys::OPERATIONS_TEAM, true],
            'Update Web' => ['update', RoleKeys::WEB_TEAM, true],
            'Update DSG' => ['update', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Create No Role' => ['create', null, false],
            'Create Operations Contributor' => ['create',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Create Operations' => ['create', RoleKeys::OPERATIONS_TEAM, true],
            'Create Web' => ['create', RoleKeys::WEB_TEAM, true],
            'Create DSG' => ['create', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Delete No Role' => ['delete', null, false],
            'Delete Operations Contributor' => ['delete',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Delete Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, true],
            'Delete Web' => ['delete', RoleKeys::WEB_TEAM, true],
            'Delete DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Delete Any No Role' => ['deleteAny', null, false],
            'Delete Any Operations Contributor' => ['deleteAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'Delete Any Operations' => ['deleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Any Web' => ['deleteAny', RoleKeys::WEB_TEAM, false],
            'Delete Any DSG' => ['deleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore No Role' => ['restore', null, false],
            'Restore Operations Contributor' => ['restore',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, true],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, true],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Restore Any No Role' => ['restoreAny', null, false],
            'Restore Any Operations Contributor' => ['restoreAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Restore Any Operations' => ['restoreAny', RoleKeys::OPERATIONS_TEAM, true],
            'Restore Any Web' => ['restoreAny', RoleKeys::WEB_TEAM, true],
            'Restore Any DSG' => ['restoreAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete Operations Contributor' => ['forceDelete',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, true],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, true],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Force Delete Any No Role' => ['forceDeleteAny', null, false],
            'Force Delete Any Operations Contributor' => ['forceDeleteAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Force Delete Any Operations' => ['forceDeleteAny', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Any Web' => ['forceDeleteAny', RoleKeys::WEB_TEAM, false],
            'Force Delete Any DSG' => ['forceDeleteAny', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Dissociate No Role' => ['dissociate', null, false],
            'Dissociate Operations Contributor' => ['dissociate',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Dissociate Operations' => ['dissociate', RoleKeys::OPERATIONS_TEAM, true],
            'Dissociate Any Web' => ['dissociate', RoleKeys::WEB_TEAM, true],
            'Dissociate DSG' => ['dissociate', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Dissociate Any No Role' => ['dissociateAny', null, false],
            'Dissociate Any Operations Contributor' => ['dissociateAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Dissociate Any Operations' => ['dissociateAny', RoleKeys::OPERATIONS_TEAM, true],
            'Dissociate Any Any Web' => ['dissociateAny', RoleKeys::WEB_TEAM, true],
            'Dissociate Any DSG' => ['dissociateAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Replicate No Role' => ['replicate', null, false],
            'Replicate Operations Contributor' => ['replicate',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Replicate Operations' => ['replicate', RoleKeys::OPERATIONS_TEAM, true],
            'Replicate Any Web' => ['replicate', RoleKeys::WEB_TEAM, true],
            'Replicate DSG' => ['replicate', RoleKeys::DIVISION_STAFF_GROUP, true],

            // Filament Special Roles
            'Attach No Role' => ['attach', null, false],
            'Attach Operations Contributor' => ['attach',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Attach Operations' => ['attach', RoleKeys::OPERATIONS_TEAM, true],
            'Attach Web' => ['attach', RoleKeys::WEB_TEAM, true],
            'Attach DSG' => ['attach', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Detach No Role' => ['detach', null, false],
            'Detach Operations Contributor' => ['detach',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Detach Operations' => ['detach', RoleKeys::OPERATIONS_TEAM, true],
            'Detach Web' => ['detach', RoleKeys::WEB_TEAM, true],
            'Detach DSG' => ['detach', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Detach Any No Role' => ['detachAny', null, false],
            'Detach Any Operations Contributor' => ['detachAny',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Detach Any Operations' => ['detachAny', RoleKeys::OPERATIONS_TEAM, true],
            'Detach Any Web' => ['detachAny', RoleKeys::WEB_TEAM, true],
            'Detach Any DSG' => ['detachAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Move Up No Role' => ['moveUp', null, false],
            'Mobe Up Operations Contributor' => ['moveUp',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Move Up Operations' => ['moveUp', RoleKeys::OPERATIONS_TEAM, true],
            'Move Up Web' => ['moveUp', RoleKeys::WEB_TEAM, true],
            'Move Up DSG' => ['moveUp', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Move Down No Role' => ['moveDown', null, false],
            'Move Down Operations Contributor' => ['moveDown',  RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'Move Down Operations' => ['moveDown', RoleKeys::OPERATIONS_TEAM, true],
            'Move Down Web' => ['moveDown', RoleKeys::WEB_TEAM, true],
            'Move Down DSG' => ['moveDown', RoleKeys::DIVISION_STAFF_GROUP, true],
        ];
    }
}
