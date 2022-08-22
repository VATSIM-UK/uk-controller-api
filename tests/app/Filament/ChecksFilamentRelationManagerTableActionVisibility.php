<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Livewire\Livewire;

trait ChecksFilamentRelationManagerTableActionVisibility
{
    /**
     * @dataProvider tableActionProvider
     */
    public function testItControlsTableActionVisibility(
        string $relationManagerClass,
        string $action,
        string $tableActionRecordClass,
        int|string $tableActionRecordId,
        string $tableActionOwnerRecordClass,
        int|string $tableActionOwnerRecordId,
        ?RoleKeys $role,
        bool $canSee
    ): void {
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

    public function tableActionProvider(): array
    {
        return tap(
            array_merge(
                $this->generateActionTestCases($this->readOnlyTableActions(), $this->readOnlyRoles()),
                $this->generateActionTestCases($this->writeTableActions(), $this->writeRoles()),
            ),
            function (array $allActions) {
                $this->assertNotEmpty($allActions);
            }
        );
    }

    private function generateActionTestCases(array $actionsByRelationManager, array $rolesThatCanPerformAction): array
    {
        $allActions = [];

        foreach ($actionsByRelationManager as $relationManager => $actions) {
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
                        $this->tableActionRecordId()[$relationManager],
                        $this->tableActionOwnerRecordClass(),
                        $this->tableActionOwnerRecordId(),
                        $role,
                        in_array(
                            $role,
                            $rolesThatCanPerformAction
                        ),
                    ];
                }
            }
        }

        return $allActions;
    }

    private function readOnlyRoles(): array
    {
        return [
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::WEB_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
            null,
        ];
    }

    private function writeRoles(): array
    {
        return [
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::WEB_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
        ];
    }

    protected abstract function tableActionRecordClass(): array;

    protected abstract function tableActionRecordId(): array;

    protected abstract function tableActionOwnerRecordClass(): string;

    protected abstract function tableActionOwnerRecordId(): int|string;

    protected function writeTableActions(): array
    {
        return [];
    }

    protected function readOnlyTableActions(): array
    {
        return [];
    }
}
