<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\PrenoteResource;
use App\Filament\Resources\PrenoteResource\Pages\ListPrenotes;
use App\Filament\Resources\PrenoteResource\RelationManagers\ControllersRelationManager;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use App\Services\ControllerPositionHierarchyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;
use App\Filament\Resources\PrenoteResource\Pages\EditPrenote;

class PrenoteResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItLoadsDataForView()
    {
        Livewire::test(PrenoteResource\Pages\ViewPrenote::class, ['record' => 1])
            ->assertSet('data.description', 'Prenote One');
    }

    public function testItCreatesAPrenote()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', 'A Prenote')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'prenotes',
            [
                'description' => 'A Prenote',
            ]
        );
    }

    public function testItDoesntCreatePrenoteIfDescriptionEmpty()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', '')
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreatePrenoteIfDescriptionTooLong()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->assertSet('data.description', 'Prenote One');
    }

    public function testItEditsAPrenote()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', 'A Prenote')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'prenotes',
            [
                'description' => 'A Prenote',
            ]
        );
    }

    public function testItDoesntEditPrenoteIfIfDescriptionEmpty()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', '')
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditPrenoteIfDescriptionTooLong()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDisplaysControllers()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
        )->assertCanSeeTableRecords([1, 2]);
    }

    public function testControllersCanBeAttachedAtTheEnd()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
        )
            ->callTableAction('attach', data: ['recordId' => 3])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeAttachedAfterAnotherController()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
        )
            ->callTableAction('attach', data: ['recordId' => 3, 'insert_after' => 1])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemoved()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemovedAtTheEnd()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
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
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrderAtTheTop()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
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
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownTheOrder()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
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
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownAtTheBottom()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1), 'pageClass' => EditPrenote::class]
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
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    protected static function resourceClass(): string
    {
        return PrenoteResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Prenote One';
    }

    protected function getCreateText(): string
    {
        return 'Create Prenote';
    }

    protected function getViewText(): string
    {
        return 'View Prenote One';
    }

    protected function getIndexText(): array
    {
        return ['Prenotes', 'Prenote One', 'Prenote Two'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return Prenote::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListPrenotes::class;
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
        return Prenote::findOrFail(1);
    }

    protected function getViewRecord(): Model
    {
        return Prenote::findOrFail(1);
    }
}
