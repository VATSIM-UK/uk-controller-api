<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;
use App\Filament\Resources\SrdRouteResource;
use App\Filament\Resources\SrdRouteResource\Pages\ListSrdRoutes;
use App\Filament\Resources\SrdRouteResource\RelationManagers\NotesRelationManager;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class SrdRouteResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksViewFilamentAccess;
    use ChecksListingFilamentAccess;

    public function testItListsNotes()
    {
        Livewire::test(
            NotesRelationManager::class,
            ['ownerRecord' => SrdRoute::findOrFail(5)]
        )
            ->assertCanSeeTableRecords([SrdNote::find(1), SrdNote::find(2), SrdNote::find(3)]);
    }

    protected function getIndexText(): array
    {
        return ['Srd Routes', 'EGLL', 'VEULE', '24500', '66000', 'MID UL612', '1,2,3'];
    }

    protected function getViewText(): string
    {
        return 'View Srd Route';
    }

    protected function getViewRecord(): Model
    {
        return SrdRoute::findOrFail(1);
    }

    protected function resourceClass(): string
    {
        return SrdRouteResource::class;
    }

    protected function resourceRecordClass(): string
    {
        return SrdRoute::class;
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function resourceListingClass(): string
    {
        return ListSrdRoutes::class;
    }

    protected function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }
}
