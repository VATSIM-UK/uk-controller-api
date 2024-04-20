<?php

namespace App\Filament;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Filament\Resources\Pages\ManageRecords;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

trait BaseChecksActionVisibility
{
    #[DataProvider('tableActionProvider')]
    public function testItControlsActionVisibility(
        callable $testCase,
        ?RoleKeys $role,
    ): void
    {
        $user = User::factory()->create();
        if ($role) {
            $user->roles()->sync(Role::idFromKey($role));
        }
        $this->actingAs($user);

        $testCase();
    }

    public static function tableActionProvider(): array
    {
        return tap(
            array_merge(
                static::generateRelationManagerTableActionTestCases(
                    static::readOnlyTableActions(),
                    static::readOnlyRoles(),
                ),
                static::generateRelationManagerTableActionTestCases(
                    static::writeTableActions(),
                    static::writeRoles(),
                ),
                static::generateResourceTableTestCases(
                    static::writeResourceTableActions(),
                    static::writeRoles(),
                ),
                static::generateResourceTableTestCases(
                    static::readOnlyResourceTableActions(),
                    static::readOnlyRoles(),
                ),
                static::generateResourcePageActionTestCases(
                    static::writeResourcePageActions(),
                    static::writeRoles(),
                ),
            ),
            function (array $allActions)
            {
                static::assertNotEmpty($allActions);
            }
        );
    }

    private static function generateRelationManagerTableActionTestCases(
        array $actionsByRelationManager,
        array $rolesThatCanPerformAction
    ): array
    {
        $allActions = [];

        foreach ($actionsByRelationManager as $relationManager => $actions) {
            foreach ($actions as $action) {
                foreach (static::rolesToIterate() as $role) {
                    $allActions[sprintf(
                        '%s, %s table action with %s role',
                        $relationManager,
                        $action,
                        $role?->value ?? 'no'
                    )] = [
                            function () use ($relationManager, $role, $action, $rolesThatCanPerformAction)
                            {
                                $livewire = Livewire::test(
                                    $relationManager,
                                    static::relationManagerLivewireParams(
                                        static::resourceRecordClass(),
                                        static::getResourceIdFromTest(),
                                    )
                                );

                                static::assertTableActionVisibility(
                                    $livewire,
                                    static::tableActionRecordClass()[$relationManager],
                                    static::tableActionRecordId()[$relationManager],
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

    private static function generateResourceTableTestCases(
        array $actions,
        array $rolesThatCanPerformAction
    ): array
    {
        $allActions = [];

        foreach ($actions as $action) {
            foreach (static::rolesToIterate() as $role) {
                $allActions[sprintf(
                    '%s, %s table action with %s role',
                    static::resourceListingClass(),
                    $action,
                    $role?->value ?? 'no'
                )] = [
                        function () use ($role, $action, $rolesThatCanPerformAction)
                        {
                            $livewire = Livewire::test(
                                static::resourceListingClass(),
                                static::resourceLivewireParams(static::getResourceIdFromTest())
                            );

                            static::assertTableActionVisibility(
                                $livewire,
                                static::resourceRecordClass(),
                                static::getResourceIdFromTest(),
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

    private static function generateResourcePageActionTestCases(
        array $actions,
        array $rolesThatCanPerformAction
    ): array
    {
        $allActions = [];

        foreach ($actions as $action) {
            foreach (static::rolesToIterate() as $role) {
                $allActions[sprintf(
                    '%s, %s page action with %s role',
                    static::resourceListingClass(),
                    $action,
                    $role?->value ?? 'no'
                )] = [
                        function () use ($role, $action, $rolesThatCanPerformAction)
                        {
                            $livewire = Livewire::test(
                                static::resourceListingClass(),
                                static::resourceLivewireParams(static::getResourceIdFromTest())
                            );

                            /*
                             * When using ManageRecords, filament doesn't put the action on the page at all, whereas
                             * assertPageActionDoesntExist will check the action exists first. So call a different method
                             * depending on what class we're testing.
                             */
                            $checkToPerform = in_array(
                                $role,
                                $rolesThatCanPerformAction
                            ) ? 'assertActionVisible'
                                : (
                                    get_parent_class(
                                        static::resourceListingClass()
                                    ) === ManageRecords::class ? 'assertActionDoesNotExist' : 'assertActionHidden');

                            $livewire->$checkToPerform($action);
                        },
                        $role,
                    ];
            }
        }

        return $allActions;
    }

    private static function assertTableActionVisibility(
        Testable $livewire,
        string $recordClass,
        string $recordId,
        string $action,
        bool $actionCanBePerformed
    ): void
    {
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

    private static function relationManagerLivewireParams(string $ownerRecordClass, int|string $ownerRecordId): array
    {
        return [
            'ownerRecord' => call_user_func(
                $ownerRecordClass . '::findOrFail',
                $ownerRecordId
            ),
            'pageClass' => call_user_func(static::resourceClass() . '::getPages')['edit']->getPage(),
        ];
    }

    private static function resourceLivewireParams(int|string $recordId): array
    {
        return [
            'record' => $recordId,
        ];
    }

    private static function getResourceIdFromTest(): int|string
    {
        return is_callable(static::resourceId())
            ? call_user_func(static::resourceId())
            : static::resourceId();
    }

    protected static function tableActionRecordClass(): array
    {
        return [];
    }

    protected static function tableActionRecordId(): array
    {
        return [];
    }

    protected static function resourceId(): int|string|callable
    {
        return '';
    }

    protected static function resourceRecordClass(): string
    {
        return '';
    }

    protected static function resourceListingClass(): string
    {
        return '';
    }

    protected static function writeTableActions(): array
    {
        return [];
    }

    protected static function readOnlyTableActions(): array
    {
        return [];
    }

    protected static function writeResourceTableActions(): array
    {
        return [];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [];
    }

    protected static function writeResourcePageActions(): array
    {
        return [];
    }
}
