<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Srd\SrdImport;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;

class SrdImportTest extends BaseFunctionalTestCase
{
    private readonly SrdImport|MockInterface $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(SrdImport::class);
        $this->app->instance(SrdImport::class, $this->mockImporter);
    }

    public function testItThrowsExceptionIfFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Import file not found: srd.csv');
        Storage::fake('imports');

        Artisan::call('srd:import srd.csv');
    }

    public function testItDeletesDataAndCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('srd.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('srd.csv', 'imports');

        $mockBuilderSrdRoutes = Mockery::mock(Builder::class);
        $mockBuilderSrdRoutes->shouldReceive('delete')->once();
        $mockBuilderSrdNotes = Mockery::mock(Builder::class);
        $mockBuilderSrdNotes->shouldReceive('delete')->once();

        DB::partialMock();
        DB::shouldReceive('table')->with('srd_routes')->once()->andReturn($mockBuilderSrdRoutes);
        DB::shouldReceive('table')->with('srd_notes')->once()->andReturn($mockBuilderSrdNotes);
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            $callback();
        });

        Artisan::call('srd:import srd.csv');
    }
}
