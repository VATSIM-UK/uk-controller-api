<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use PHPUnit\Framework\Attributes\DataProvider;

trait BaseChecksCreateAccess
{
    use HasResourceClass;

    #[DataProvider('createRoleProvider')]
    public function testItCanOnlyBeCreatedByCertainRoles(?RoleKeys $role, bool $expectSuccess)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $response = $this->get(call_user_func($this->resourceClass() . '::getUrl', 'create'));
        if ($expectSuccess) {
            $response->assertSuccessful()
                ->assertSeeText($this->getCreateText());
        } else {
            $response->assertForbidden();
        }
    }

    /**
     * The text we expect to see on successful create load.
     */
    protected abstract function getCreateText(): string;
}
