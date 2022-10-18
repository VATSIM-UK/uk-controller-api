<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;

trait ChecksListingFilamentAccess
{
    use HasResourceClass;

    /**
     * @dataProvider indexRoleProvider
     */
    public function testItCanBeIndexed(?RoleKeys $role, bool $canIndex)
    {
        $this->beforeListing();
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        if (!$canIndex) {
            $this->get(call_user_func($this->resourceClass() . '::getUrl'))
                ->assertForbidden();
        } else {
            $this->get(call_user_func($this->resourceClass() . '::getUrl'))
                ->assertSuccessful()
                ->assertSeeText($this->getIndexText());
        }
    }

    private function indexRoleProvider(): array
    {
        return [
            'None' => [null, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    /**
     * The text we expect to see on successful index load.
     */
    protected abstract function getIndexText(): array;

    /**
     * Can be overridden to provide a test fixture for the test if needed.
     */
    protected function beforeListing(): void
    {

    }
}
