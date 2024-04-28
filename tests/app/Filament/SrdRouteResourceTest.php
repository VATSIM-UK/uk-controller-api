<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;
use App\Filament\Resources\SrdRouteResource;
use App\Filament\Resources\SrdRouteResource\Pages\ListSrdRoutes;
use App\Filament\Resources\SrdRouteResource\Pages\ViewSrdRoute;
use App\Filament\Resources\SrdRouteResource\RelationManagers\NotesRelationManager;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class SrdRouteResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentActionVisibility;
    use ChecksViewFilamentAccess;
    use ChecksListingFilamentAccess;

    public function testItFiltersByOrigin()
    {
        Livewire::test(ListSrdRoutes::class)
            ->assertCanSeeTableRecords(
                [SrdRoute::find(1), SrdRoute::find(2), SrdRoute::find(3), SrdRoute::find(4), SrdRoute::find(5)]
            )
            ->filterTable('origin', ['isActive' => 'EGAA'])
            ->assertCanSeeTableRecords([SrdRoute::find(5)])
            ->assertCanNotSeeTableRecords([SrdRoute::find(1), SrdRoute::find(2), SrdRoute::find(3), SrdRoute::find(4)]);
    }

    public function testItFiltersByDestination()
    {
        Livewire::test(ListSrdRoutes::class)
            ->assertCanSeeTableRecords(
                [SrdRoute::find(1), SrdRoute::find(2), SrdRoute::find(3), SrdRoute::find(4), SrdRoute::find(5)]
            )
            ->filterTable('destination', ['isActive' => 'VEULE'])
            ->assertCanSeeTableRecords([SrdRoute::find(4)])
            ->assertCanNotSeeTableRecords([SrdRoute::find(1), SrdRoute::find(2), SrdRoute::find(3), SrdRoute::find(5)]);
    }

    public function testItFiltersByLevel()
    {
        Livewire::test(ListSrdRoutes::class)
            ->assertCanSeeTableRecords(
                [SrdRoute::find(1), SrdRoute::find(2), SrdRoute::find(3), SrdRoute::find(4), SrdRoute::find(5)]
            )
            ->filterTable('level', ['isActive' => 18000])
            ->assertCanSeeTableRecords([SrdRoute::find(1), SrdRoute::find(2)])
            ->assertCanNotSeeTableRecords([SrdRoute::find(3), SrdRoute::find(4), SrdRoute::find(5)]);
    }

    public function testItListsNotes()
    {
        Livewire::test(
            NotesRelationManager::class,
            ['ownerRecord' => SrdRoute::findOrFail(5), 'pageClass' => ViewSrdRoute::class]
        )
            ->assertCanSeeTableRecords([SrdNote::find(1), SrdNote::find(2), SrdNote::find(3)]);
    }

    protected function getIndexText(): array
    {
        return ['Srd Routes', 'EGLL', 'VEULE', '24500', '66000', 'MID UL612', '1, 2, 3'];
    }

    protected function getViewText(): string
    {
        return 'View Srd Route';
    }

    protected function getViewRecord(): Model
    {
        return SrdRoute::findOrFail(1);
    }

    protected static function resourceClass(): string
    {
        return SrdRouteResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return SrdRoute::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceListingClass(): string
    {
        return ListSrdRoutes::class;
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }
}
