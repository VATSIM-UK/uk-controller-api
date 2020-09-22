<?php

namespace App\Imports\Wake;

use App\BaseFunctionalTestCase;
use App\Exceptions\InvalidWakeImportException;
use App\Models\Aircraft\Aircraft;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use Mockery;

class ImporterTest extends BaseFunctionalTestCase
{
    private $import;

    public function setUp(): void
    {
        parent::setUp();
        // Empty the table first
        Aircraft::all()->each(
            function (Aircraft $aircraft) {
                $aircraft->delete();
            }
        );
        $this->import = new Importer();
        $this->import->withOutput(Mockery::spy(OutputStyle::class));
    }

    public function testItCreatesAWakeCategoryListing()
    {
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => 'LIGHT'
                ]
            );

        $this->import->collection($rows);
        $aircraft = Aircraft::all();
        $this->assertCount(1, $aircraft);
        $this->assertEquals('AXXX', $aircraft->first()->code);
        $this->assertEquals(1, $aircraft->first()->wake_category_id);
    }

    public function testItUpdatesAWakeCategoryListing()
    {
        Aircraft::create(['code' => 'AXXX', 'wake_category_id' => 3]);
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => 'LIGHT'
                ]
            );

        $this->import->collection($rows);
        $aircraft = Aircraft::all();
        $this->assertCount(1, $aircraft);
        $this->assertEquals('AXXX', $aircraft->first()->code);
        $this->assertEquals(1, $aircraft->first()->wake_category_id);
    }

    public function testItImportsMultipleListings()
    {
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => 'LIGHT'
                ]
            )
            ->push(
                [
                    'icao_type_designator' => 'BZZZ',
                    'uk_arrival_wtc' => 'LOWER MEDIUM'
                ]
            );

        $this->import->collection($rows);
        $aircraft = Aircraft::all();
        $this->assertCount(2, $aircraft);

        $firstAircraft = $aircraft->first();
        $this->assertEquals('AXXX', $firstAircraft->code);
        $this->assertEquals(1, $firstAircraft->wake_category_id);

        $secondAircraft = $aircraft->get(1);
        $this->assertEquals('BZZZ', $secondAircraft->code);
        $this->assertEquals(3, $secondAircraft->wake_category_id);
    }

    /**
     * @dataProvider wakeTypesProvider
     */
    public function testItImportsAllUkWakeTypes(string $wakeType, int $expectedTypeId)
    {
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => $wakeType
                ]
            );

        $this->import->collection($rows);
        $aircraft = Aircraft::all();

        $this->assertEquals('AXXX', $aircraft->first()->code);
        $this->assertEquals($expectedTypeId, $aircraft->first()->wake_category_id);
    }

    public function wakeTypesProvider(): array
    {
        return [
            [
                'LIGHT',
                1,
            ],
            [
                'SMALL',
                2,
            ],
            [
                'LOWER MEDIUM',
                3,
            ],
            [
                'UPPER MEDIUM',
                4,
            ],
            [
                'HEAVY',
                5,
            ],
            [
                'SUPER',
                6,
            ],
        ];
    }

    public function testItThrowsExceptionOnMissingType()
    {
        $this->expectException(InvalidWakeImportException::class);
        $rows = (new Collection())
            ->push(
                [
                    'not_icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => 'LIGHT'
                ]
            );

        $this->import->collection($rows);
    }

    public function testItThrowsExceptionOnMissingWakeCategory()
    {
        $this->expectException(InvalidWakeImportException::class);
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'not_uk_arrival_wtc' => 'LIGHT'
                ]
            );

        $this->import->collection($rows);
    }

    public function testItIgnoresInvalidWakeTypes()
    {
        $rows = (new Collection())
            ->push(
                [
                    'icao_type_designator' => 'AXXX',
                    'uk_arrival_wtc' => 'NOT_LIGHT'
                ]
            );

        $this->import->collection($rows);
        $this->assertEmpty(Aircraft::all());
    }
}
