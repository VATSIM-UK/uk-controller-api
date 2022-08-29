<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Rather than repeating the same tests for every class that follows the default Filament access policy,
 * this trait provides the tests and exposes a few methods to customise them.
 */
trait ChecksDefaultFilamentAccess
{
    /**
     * @dataProvider indexRoleProvider
     */
    public function testItCanBeIndexed(?RoleKeys $role)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->get(call_user_func($this->getResourceClass() . '::getUrl'))
            ->assertSuccessful()
            ->assertSeeText($this->getIndexText());
    }

    private function indexRoleProvider(): array
    {
        return [
            'None' => [null],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP],
            'Web' => [RoleKeys::WEB_TEAM],
            'Operations' => [RoleKeys::OPERATIONS_TEAM],
        ];
    }

    /**
     * @dataProvider viewRoleProvider
     */
    public function testItCanBeViewed(?RoleKeys $role)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        $this->get(
            call_user_func($this->getResourceClass() . '::getUrl', 'view', ['record' => $this->getViewEditRecord()])
        )
            ->assertSuccessful()
            ->assertSeeText($this->getViewText());
    }

    private function viewRoleProvider(): array
    {
        return [
            'None' => [null],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP],
            'Web' => [RoleKeys::WEB_TEAM],
            'Operations' => [RoleKeys::OPERATIONS_TEAM],
        ];
    }

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
            call_user_func($this->getResourceClass() . '::getUrl', 'edit', ['record' => $this->getViewEditRecord()])
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
     * Gets the record to use for edit / view tests.
     */
    protected abstract function getViewEditRecord(): Model;

    /**
     * Get the resource this test is for.
     */
    protected abstract function getResourceClass(): string;

    /**
     * The text we expect to see on successful edit load.
     */
    protected abstract function getEditText(): string;

    /**
     * The text we expect to see on successful create load.
     */
    protected abstract function getCreateText(): string;

    /**
     * The text we expect to see on successful view load.
     */
    protected abstract function getViewText(): string;

    /**
     * The text we expect to see on successful index load.
     */
    protected abstract function getIndexText(): array;
}
