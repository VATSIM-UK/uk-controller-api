<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

trait ChecksViewFilamentAccess
{
    use HasResourceClass;

    /**
     * @dataProvider viewRoleProvider
     */
    public function testItCanBeViewed(?RoleKeys $role, bool $canView)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        if ($canView) {
            $this->get(
                call_user_func($this->resourceClass() . '::getUrl', 'view', ['record' => $this->getViewRecord()])
            )
                ->assertSuccessful()
                ->assertSeeText($this->getViewText());
        } else {
            $this->get(
                call_user_func($this->resourceClass() . '::getUrl', 'view', ['record' => $this->getViewRecord()])
            )
                ->assertForbidden();
        }
    }

    public static function viewRoleProvider(): array
    {
        return [
            'None' => [null, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    /**
     * The text we expect to see on successful view load.
     */
    protected abstract function getViewText(): string;

    /**
     * Gets the record to use for view tests.
     */
    protected abstract function getViewRecord(): Model;
}
