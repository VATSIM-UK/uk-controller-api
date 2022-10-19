<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\VersionResource;

class VersionResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;

    protected function getIndexText(): array
    {
        return ['Versions', '2.0.1', 'Stable', 'Mon 04 Dec 2017, 00:00:00'];
    }

    protected function resourceClass(): string
    {
        return VersionResource::class;
    }
}
