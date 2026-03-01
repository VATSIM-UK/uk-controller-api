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
            'event_start' => '2024-01-01 09:00:00',
            'event_finish' => '2024-01-01 18:00:00',
            'reservations' => [
                [
                    'airport' => 'EGLL',
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

    public function testItCallsImporterForSlotBasedJson()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('slots.json', json_encode([
            'event_start' => '2024-01-01 09:00:00',
            'event_finish' => '2024-01-01 18:00:00',
            'stand_slots' => [
                [
                    'airport' => 'EGLL',
                    'stand' => '531',
                    'slot_reservations' => [
                        [
                            'callsign' => 'BAW1234',
                            'slotstart' => '2024-01-01 09:00:00',
                            'slotend' => '2024-01-01 09:30:00',
                        ],
                        [
                            'callsign' => 'BAW4321',
                            'slotstart' => '2024-01-01 09:31:00',
                            'slotend' => '2024-01-01 10:00:00',
                        ],
                    ],
                ],
            ],
        ]));

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('collection')
            ->with(Mockery::on(function (Collection $rows): bool {
                if ($rows->count() !== 2) {
                    return false;
                }

                $first = $rows->first();
                $second = $rows->last();

                return $first->get('airport') === 'EGLL'
                    && $first->get('stand') === '531'
                    && $first->get('callsign') === 'BAW1234'
                    && $first->get('start') === '2024-01-01 09:00:00'
                    && $first->get('end') === '2024-01-01 09:30:00'
                    && $second->get('airport') === 'EGLL'
                    && $second->get('stand') === '531'
                    && $second->get('callsign') === 'BAW4321';
            }))
            ->once();

        $this->assertEquals(0, Artisan::call('stand-reservations:import slots.json'));
    }

}
