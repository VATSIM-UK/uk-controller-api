<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;
use App\Filament\Resources\SrdRouteResource;
use App\Filament\Resources\SrdRouteResource\Pages\ListSrdRoutes;
use App\Models\Srd\SrdRoute;
use Illuminate\Database\Eloquent\Model;

class SrdRouteResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksViewFilamentAccess;
    use ChecksListingFilamentAccess;

    protected function getIndexText(): array
    {
        return ['Srd Routes', 'EGLL', 'VEULE', '24500', '66000', 'MID UL612'];
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
