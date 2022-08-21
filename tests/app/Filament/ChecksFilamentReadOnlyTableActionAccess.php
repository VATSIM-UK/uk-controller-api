<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Livewire\Livewire;

trait ChecksFilamentReadOnlyTableActionAccess
{
    /**
     * @dataProvider readOnlyActionProvider
     */
    public function testItShowsReadOnlyTableActions(
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

    public function readOnlyActionProvider(): array
    {
        $readActions = $this->readOnlyTableActions();

        $allActions = [];
        foreach ($readActions as $relationManager => $actions) {
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
                        $this->tableActionRecordClass(),
                        $this->tableActionRecordId(),
                        $this->tableActionOwnerRecordClass(),
                        $this->tableActionOwnerRecordId(),
                        $role,
                        true,
                    ];
                }
            }
        }

        return $allActions;
    }

    protected abstract function tableActionRecordClass(): string;

    protected abstract function tableActionRecordId(): int|string;

    protected abstract function tableActionOwnerRecordClass(): string;

    protected abstract function tableActionOwnerRecordId(): int|string;

    protected abstract function readOnlyTableActions(): array;
}
