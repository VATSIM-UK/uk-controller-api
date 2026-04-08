<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\Handoffs\HandoffResource;
use App\Filament\Resources\Handoffs\Pages\CreateHandoff;
use App\Filament\Resources\Handoffs\Pages\EditHandoff;
use App\Filament\Resources\Handoffs\Pages\ListHandoffs;
use App\Filament\Resources\Handoffs\Pages\ViewHandoff;
use App\Filament\Resources\Handoffs\RelationManagers\ControllersRelationManager;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Services\ControllerPositionHierarchyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class HandoffResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewHandoff::class, ['record' => 1])
            ->assertSet('data.description', 'foo');
    }

    public function testItCreatesAHandoff()
    {
        Livewire::test(CreateHandoff::class)
            ->set('data.description', 'A Handoff')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'handoffs',
            [
                'description' => 'A Handoff',
            ]
        );
    }

    public function testItDoesntCreateHandoffIfDescriptionEmpty()
    {
        Livewire::test(CreateHandoff::class)
            ->set('data.description', '')
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreateHandoffIfDescriptionTooLong()
    {
        Livewire::test(CreateHandoff::class)
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditHandoff::class, ['record' => 1])
            ->assertSet('data.description', 'foo');
    }

    public function testItEditsAHandoff()
    {
        Livewire::test(EditHandoff::class, ['record' => 1])
            ->set('data.description', 'A Handoff')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'handoffs',
            [
                'description' => 'A Handoff',
            ]
        );
    }

    public function testItDoesntEditHandoffIfDescriptionEmpty()
    {
        Livewire::test(EditHandoff::class, ['record' => 1])
            ->set('data.description', '')
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditHandoffIfDescriptionTooLong()
    {
        Livewire::test(EditHandoff::class, ['record' => 1])
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDisplaysControllers()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )->assertCanSeeTableRecords([1, 2]);
    }

    public function testControllersCanBeAttachedAtTheEnd()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('attach', data: ['recordId' => 3])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeAttachedAfterAnotherController()
    {
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('attach', data: ['recordId' => 3, 'insert_after' => 1])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemoved()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemovedAtTheEnd()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_N_APP',
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrderAtTheTop()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(1))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
                'LON_C_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownAtTheBottom()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            ControllersRelationManager::class,
            ['ownerRecord' => Handoff::findOrFail(1), 'pageClass' => EditHandoff::class]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Handoff::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    protected static function resourceClass(): string
    {
        return HandoffResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit foo';
    }

    protected function getCreateText(): string
    {
        return 'Create Handoff';
    }

    protected function getViewText(): string
    {
        return 'View foo';
    }

    protected function getIndexText(): array
    {
        return ['Handoffs', 'foo', 'EGLL_S_TWR', 'EGLL_N_APP', 'LON_S_CTR'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return Handoff::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListHandoffs::class;
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function tableActionRecordClass(): array
    {
        return [ControllersRelationManager::class => ControllerPosition::class];
    }

    protected static function tableActionRecordId(): array
    {
        return [ControllersRelationManager::class => 1];
    }

    protected static function writeTableActions(): array
    {
        return [
            ControllersRelationManager::class => [
                'attach',
                'detach',
                'moveUp',
                'moveDown',
            ],
        ];
    }

    protected function getEditRecord(): Model
    {
        return Handoff::find(1);
    }

    protected function getViewRecord(): Model
    {
        return Handoff::find(1);
    }
}
