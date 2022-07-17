<?php

namespace App\Policies;

use App\BaseFunctionalTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;

class DefaultFilamentPolicyTest extends BaseFunctionalTestCase
{
    private readonly DefaultFilamentPolicy $defaultFilamentPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->defaultFilamentPolicy = $this->app->make(DefaultFilamentPolicy::class);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testItManagesAccess(string $action, ?RoleKeys $role, bool $expected)
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->assertEquals($expected, $this->defaultFilamentPolicy->$action($user));
    }

    public function dataProvider(): array
    {
        return [
            'View No Role' => ['view', null, true],
            'View Operations' => ['view', RoleKeys::OPERATIONS_TEAM, true],
            'View Web' => ['view', RoleKeys::WEB_TEAM, true],
            'View DSG' => ['view', RoleKeys::DIVISION_STAFF_GROUP, true],
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
            'Restore No Role' => ['restore', null, false],
            'Restore Operations' => ['restore', RoleKeys::OPERATIONS_TEAM, true],
            'Restore Web' => ['restore', RoleKeys::WEB_TEAM, true],
            'Restore DSG' => ['restore', RoleKeys::DIVISION_STAFF_GROUP, true],
            'Force Delete No Role' => ['forceDelete', null, false],
            'Force Delete Operations' => ['forceDelete', RoleKeys::OPERATIONS_TEAM, true],
            'Force Delete Web' => ['forceDelete', RoleKeys::WEB_TEAM, true],
            'Force Delete DSG' => ['forceDelete', RoleKeys::DIVISION_STAFF_GROUP, true],
        ];
    }
}
