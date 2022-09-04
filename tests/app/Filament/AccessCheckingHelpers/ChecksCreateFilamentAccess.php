<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;

trait ChecksCreateFilamentAccess
{
    use HasResourceClass;

    /**
     * @dataProvider createRoleProvider
     */
    public function testItCanOnlyBeCreatedByCertainRoles(?RoleKeys $role, bool $expectSuccess)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $response = $this->get(call_user_func($this->getResourceClass() . '::getUrl', 'create'));
        if ($expectSuccess) {
            $response->assertSuccessful()
                ->assertSeeText($this->getCreateText());
        } else {
            $response->assertForbidden();
        }
    }

    private function createRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    /**
     * The text we expect to see on successful create load.
     */
    protected abstract function getCreateText(): string;
}
