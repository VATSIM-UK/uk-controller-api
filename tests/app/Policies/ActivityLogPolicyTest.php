<?php

namespace App\Policies;

use App\BaseFunctionalTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

class ActivityLogPolicyTest extends BaseFunctionalTestCase
{
    private readonly ActivityLogPolicy $activityLogPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->activityLogPolicy = $this->app->make(ActivityLogPolicy::class);
    }

    #[DataProvider('dataProvider')]
    public function testItManagesAccess(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->activityLogPolicy->$action($user));
    }

    public static function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, false],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, false],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
            'View Any No Role' => ['viewAny', null, false],
            'View Any Operations' => ['viewAny', RoleKeys::OPERATIONS_TEAM, false],
            'View Any Web' => ['viewAny', RoleKeys::WEB_TEAM, true],
            'View Any DSG' => ['viewAny', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Update No Role' => ['update', null, false],
            'Update Operations' => ['update', RoleKeys::OPERATIONS_TEAM, false],
            'Update Web' => ['update', RoleKeys::WEB_TEAM, false],
            'Update DSG' => ['update', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Create No Role' => ['create', null, false],
            'Create Operations' => ['create', RoleKeys::OPERATIONS_TEAM, false],
            'Create Web' => ['create', RoleKeys::WEB_TEAM, false],
            'Create DSG' => ['create', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete No Role' => ['delete', null, false],
            'Delete Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Web' => ['delete', RoleKeys::WEB_TEAM, false],
            'Delete DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Delete Any No Role' => ['delete', null, false],
            'Delete Any Operations' => ['delete', RoleKeys::OPERATIONS_TEAM, false],
            'Delete Any Web' => ['delete', RoleKeys::WEB_TEAM, false],
            'Delete Any DSG' => ['delete', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Restore No Role' => ['restore', null, false],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, false],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, false],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, false],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, false],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, false],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, false],
        ];
    }
}
