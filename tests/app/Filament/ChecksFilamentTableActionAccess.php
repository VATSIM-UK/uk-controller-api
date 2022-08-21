<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Livewire\Livewire;

trait ChecksFilamentTableActionAccess
{
    /**
     * @dataProvider writeOnlyActionProvider
     */
    public function testItShowsWriteOnlyTableActions(
        string $relationManagerClass,
        string $action,
        string $tableActionRecordClass,
        int|string $tableActionRecordId,
        string $tableActionOwnerRecordClass,
        int|string $tableActionOwnerRecordId,
        ?RoleKeys $role,
        bool $canSee
    ) {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync(Role::idFromKey($role));
        }
        $this->actingAs($user);

        $livewire = Livewire::test(
            $relationManagerClass,
            [
                'ownerRecord' => call_user_func(
                    $tableActionOwnerRecordClass . '::findOrFail',
                    $tableActionOwnerRecordId
                ),
            ]
        );

        $actionRecord = call_user_func($tableActionRecordClass . '::findOrFail', $tableActionRecordId);
        if ($canSee) {
            $livewire->assertTableActionVisible($action, $actionRecord);
        } else {
            $livewire->assertTableActionHidden($action, $actionRecord);
        }
    }

    public function writeOnlyActionProvider(): array
    {
        $allActions = [];
        foreach ($this->writeTableActions() as $relationManager => $actions) {
            foreach ($actions as $action) {
                foreach (RoleKeys::cases() as $role) {
                    $allActions[sprintf(
                        '%s, %s action with %s role',
                        $relationManager,
                        $action,
                        $role?->value ?? 'No'
                    )] = [
                        $relationManager,
                        $action,
                        $this->tableActionRecordClass()[$relationManager],
                        $this->tableActionRecordId(),
                        $this->tableActionOwnerRecordClass(),
                        $this->tableActionOwnerRecordId(),
                        $role,
                        in_array(
                            $role,
                            [RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::OPERATIONS_TEAM, RoleKeys::WEB_TEAM]
                        ),
                    ];
                }
            }
        }

        return $allActions;
    }

    protected abstract function tableActionRecordClass(): array;

    protected abstract function tableActionRecordId(): int|string;

    protected abstract function tableActionOwnerRecordClass(): string;

    protected abstract function tableActionOwnerRecordId(): int|string;

    protected abstract function writeTableActions(): array;
}
