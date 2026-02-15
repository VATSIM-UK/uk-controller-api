<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
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

    public function testItCallsImporterForCsv()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('stands.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('stands.csv', 'imports', Excel::CSV)
            ->once();

        $this->assertEquals(0, Artisan::call('stand-reservations:import stands.csv'));
    }

    public function testItCallsImporterForJson()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('stands.json', json_encode([
            'start' => '2024-01-01 09:00:00',
            'end' => '2024-01-01 18:00:00',
            'reservations' => [
                [
                    'airfield' => 'EGLL',
                    'stand' => '1L',
                    'callsign' => 'BAW24A',
                ],
            ],
        ]));

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('collection')
            ->with(Mockery::type(Collection::class))
            ->once();

        $this->assertEquals(0, Artisan::call('stand-reservations:import stands.json'));
    }
}
