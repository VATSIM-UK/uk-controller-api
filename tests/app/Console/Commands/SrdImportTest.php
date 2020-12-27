<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Srd\SrdImport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Mockery;

class SrdImportTest extends BaseFunctionalTestCase
{
    private $mockImporter;

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

    public function testItTruncatesTablesAndCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('srd.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('srd.csv', 'imports');

        DB::partialMock();
        DB::shouldReceive('statement')->with('SET foreign_key_checks=0')->once();

        DB::shouldReceive('table')->with('srd_note_srd_route')
            ->andReturnSelf()
            ->once();

        DB::shouldReceive('table')->with('srd_notes')
            ->andReturnSelf()
            ->once();

        DB::shouldReceive('table')->with('srd_routes')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('truncate')->andReturnSelf()->times(3);

        DB::shouldReceive('statement')->with('SET foreign_key_checks=1')->once();

        Artisan::call('srd:import srd.csv');
    }
}
