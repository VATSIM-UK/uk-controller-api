<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\DataProvider;

trait BaseChecksEditAccess
{
    #[DataProvider('editRoleProvider')]
    public function testItCanOnlyBeEditedByCertainRoles(?RoleKeys $role, bool $expectSuccess)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $response = $this->get(
            call_user_func($this->resourceClass() . '::getUrl', 'edit', ['record' => $this->getEditRecord()])
        );
        if ($expectSuccess) {
            $response->assertSuccessful()->assertSeeText($this->getEditText());
        } else {
            $response->assertForbidden();
        }
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
