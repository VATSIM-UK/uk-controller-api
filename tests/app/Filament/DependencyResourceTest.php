<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\DependencyResource;
use App\Filament\Resources\DependencyResource\Pages\ListDependencies;
use App\Models\Dependency\Dependency;
use App\Services\DependencyService;
use Livewire\Livewire;

class DependencyResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentActionVisibility;
    use ChecksListingFilamentAccess;

    public function testItListsDependencies()
    {
        Livewire::test(ListDependencies::class)
            ->assertCanSeeTableRecords(Dependency::all());
    }

    public function testItDownloadsDependencies()
    {
        Livewire::test(ListDependencies::class)
            ->callTableAction('download-dependency', Dependency::findOrFail(1))
            ->assertFileDownloaded(
                'one.json',
                json_encode(DependencyService::fetchDependencyDataById(1)),
                'application/json'
            );
    }

    protected function getIndexText(): array
    {
        return ['Dependencies', 'DEPENDENCY_ONE', 'DEPENDENCY_TWO'];
    }

    protected static function resourceClass(): string
    {
        return DependencyResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return Dependency::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceListingClass(): string
    {
        return ListDependencies::class;
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'download-dependency',
        ];
    }
}
