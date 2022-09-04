<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

trait ChecksEditFilamentAccess
{
    /**
     * @dataProvider editRoleProvider
     */
    public function testItCanOnlyBeEditedByCertainRoles(?RoleKeys $role, bool $expectSuccess)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $response = $this->get(
            call_user_func($this->getResourceClass() . '::getUrl', 'edit', ['record' => $this->getEditRecord()])
        );
        if ($expectSuccess) {
            $response->assertSuccessful()->assertSeeText($this->getEditText());
        } else {
            $response->assertForbidden();
        }
    }

    private function editRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    /**
     * Gets the record to use for edit  tests.
     */
    protected abstract function getEditRecord(): Model;

    /**
     * The text we expect to see on successful edit load.
     */
    protected abstract function getEditText(): string;
}
