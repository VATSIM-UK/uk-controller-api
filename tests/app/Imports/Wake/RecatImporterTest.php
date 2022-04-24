<?php

namespace App\Imports\Wake;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Mockery;

class RecatImporterTest extends BaseFunctionalTestCase
{
    private $import;

    public function setUp(): void
    {
        parent::setUp();
        $this->import = new RecatImporter();
        $this->import->withOutput(Mockery::spy(OutputStyle::class));
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItHandlesBadData(Collection $data, string $expectedError)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedError);
        $this->import->collection($data);
    }

    public function badDataProvider(): array
    {
        return [
            'Empty row' => [
                collect()->push([]),
                'Invalid RECAT import data: '
            ],
            'Not an array' => [
                collect()->push(''),
                'Invalid RECAT import data: Not an array'
            ],
            'Missing item' => [
                collect()->push(
                    [
                        'B738'
                    ]
                ),
                'Invalid RECAT import data: B738'
            ],
            'Invalid category' => [
                collect()->push(
                    [
                        'B738',
                        'XXX'
                    ]
                ),
                'RECAT category not found: B738,XXX'
            ],
        ];
    }

    public function testItUpdatesRecatCategories()
    {
        $this->import->collection(collect()->push(['B738', 'U']));

        $this->assertDatabaseHas(
            'aircraft_wake_category',
            [
                'aircraft_id' => Aircraft::where('code', 'B738')->first()->id,
                'wake_category_id' => WakeCategory::where('code', 'U')->first()->id,
            ]
        );

        $this->assertDatabaseHas(
            'aircraft_wake_category',
            [
                'aircraft_id' => Aircraft::where('code', 'B738')->first()->id,
                'wake_category_id' => WakeCategory::where('code', 'LM')->first()->id,
            ]
        );
    }

    public function testItHandlesUnknownAircraftTypes()
    {
        $this->expectNotToPerformAssertions();
        $this->import->collection(collect()->push(['XXXX', 'U']));
    }
}
