<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Mockery;

class StandReservationsImportTest extends BaseFunctionalTestCase
{
    private $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(Importer::class);
        $this->app->instance(Importer::class, $this->mockImporter);
    }

    public function testItReturnsErrorIfFileNotFound()
    {
        Storage::fake('imports');
        $this->assertEquals(1, Artisan::call('stand-reservations:import stands.csv'));
    }

    public function testItCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('stands.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('stands.csv', 'imports', Excel::CSV)
            ->once();

        Artisan::call('stand-reservations:import stands.csv');
    }
}
