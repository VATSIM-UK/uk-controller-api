<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;

trait ChecksFilamentActionVisibility
{
    /**
     * @dataProvider tableActionProvider
     */
    public function testItControlsActionVisibility(
        callable $testCase,
        ?RoleKeys $role,
    ): void {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync(Role::idFromKey($role));
        }
        $this->actingAs($user);

        $testCase();
    }

    public function tableActionProvider(): array
    {
        return tap(
            array_merge(
                $this->generateRelationManagerTableActionTestCases(
                    $this->readOnlyTableActions(),
                    $this->readOnlyRoles(),
                ),
                $this->generateRelationManagerTableActionTestCases(
                    $this->writeTableActions(),
                    $this->writeRoles(),
                ),
                $this->generateResourceTableTestCases(
                    $this->writeResourceTableActions(),
                    $this->writeRoles(),
                ),
                $this->generateResourceTableTestCases(
                    $this->readOnlyResourceTableActions(),
                    $this->readOnlyRoles(),
                ),
                $this->generateResourcePageActionTestCases(
                    $this->writeResourcePageActions(),
                    $this->writeRoles(),
                ),
            ),
            function (array $allActions) {
                $this->assertNotEmpty($allActions);
            }
        );
    }

    private function generateRelationManagerTableActionTestCases(
        array $actionsByRelationManager,
        array $rolesThatCanPerformAction
    ): array {
        $allActions = [];

        foreach ($actionsByRelationManager as $relationManager => $actions) {
            foreach ($actions as $action) {
                foreach ($this->rolesToIterate() as $role) {
                    $allActions[sprintf(
                        '%s, %s table action with %s role',
                        $relationManager,
                        $action,
                        $role?->value ?? 'no'
                    )] = [
                        function () use ($relationManager, $role, $action, $rolesThatCanPerformAction) {
                            $livewire = Livewire::test(
                                $relationManager,
                                $this->relationManagerLivewireParams(
                                    $this->resourceRecordClass(),
                                    $this->resourceId(),
                                )
                            );

                            $this->assertTableActionVisibility(
                                $livewire,
                                $this->tableActionRecordClass()[$relationManager],
                                $this->tableActionRecordId()[$relationManager],
                                $action,
                                in_array(
                                    $role,
                                    $rolesThatCanPerformAction
                                )
                            );
                        },
                        $role,
                    ];
                }
            }
        }

        return $allActions;
    }

    private function generateResourceTableTestCases(
        array $actions,
        array $rolesThatCanPerformAction
    ): array {
        $allActions = [];

        foreach ($actions as $action) {
            foreach ($this->rolesToIterate() as $role) {
                $allActions[sprintf(
                    '%s, %s table action with %s role',
                    $this->resourceListingClass(),
                    $action,
                    $role?->value ?? 'no'
                )] = [
                    function () use ($role, $action, $rolesThatCanPerformAction) {
                        $livewire = Livewire::test(
                            $this->resourceListingClass(),
                            $this->resourceLivewireParams($this->resourceId())
                        );

                        $this->assertTableActionVisibility(
                            $livewire,
                            $this->resourceRecordClass(),
                            $this->resourceId(),
                            $action,
                            in_array(
                                $role,
                                $rolesThatCanPerformAction
                            )
                        );
                    },
                    $role,
                ];
            }
        }

        return $allActions;
    }

    private function generateResourcePageActionTestCases(
        array $actions,
        array $rolesThatCanPerformAction
    ): array {
        $allActions = [];

        foreach ($actions as $action) {
            foreach ($this->rolesToIterate() as $role) {
                $allActions[sprintf(
                    '%s, %s page action with %s role',
                    $this->resourceListingClass(),
                    $action,
                    $role?->value ?? 'no'
                )] = [
                    function () use ($role, $action, $rolesThatCanPerformAction) {
                        $livewire = Livewire::test(
                            $this->resourceListingClass(),
                            $this->resourceLivewireParams($this->resourceId())
                        );

                        $canPerformAction = in_array(
                            $role,
                            $rolesThatCanPerformAction
                        );

                        if ($canPerformAction) {
                            $livewire->assertPageActionVisible($action);
                        } else {
                            $livewire->assertPageActionHidden($action);
                        }
                    },
                    $role,
                ];
            }
        }

        return $allActions;
    }

    private function assertTableActionVisibility(
        TestableLivewire $livewire,
        string $recordClass,
        string $recordId,
        string $action,
        bool $actionCanBePerformed
    ): void {
        $actionRecord = call_user_func(
            $recordClass . '::findOrFail',
            $recordId
        );

        if ($actionCanBePerformed) {
            $livewire->assertTableActionVisible($action, $actionRecord);
        } else {
            $livewire->assertTableActionHidden($action, $actionRecord);
        }
    }

    private function relationManagerLivewireParams(string $ownerRecordClass, int|string $ownerRecordId): array
    {
        return [
            'ownerRecord' => call_user_func(
                $ownerRecordClass . '::findOrFail',
                $ownerRecordId
            ),
        ];
    }

    private function resourceLivewireParams(int|string $recordId): array
    {
        return [
            'record' => $recordId,
        ];
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

    private function rolesToIterate(): array
    {
        return [
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::WEB_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
            null,
        ];
    }

    protected function tableActionRecordClass(): array
    {
        return [];
    }

    protected function tableActionRecordId(): array
    {
        return [];
    }

    protected function resourceId(): int|string
    {
        return '';
    }

    protected function resourceRecordClass(): string
    {
        return '';
    }

    protected function resourceListingClass(): string
    {
        return '';
    }

    protected function writeTableActions(): array
    {
        return [];
    }

    protected function readOnlyTableActions(): array
    {
        return [];
    }

    protected function writeResourceTableActions(): array
    {
        return [];
    }

    protected function readOnlyResourceTableActions(): array
    {
        return [];
    }

    protected function writeResourcePageActions(): array
    {
        return [];
    }
}
