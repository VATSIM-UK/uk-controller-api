<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use Livewire\Livewire;

class DepartureStandFinderTest extends BaseFilamentTestCase
{
    public function testItRenders()
    {
        Livewire::test(DepartureStandFinder::class)
            ->assertOk();
    }

    public function testItShowsOccupancyWhenAStandIsFound()
    {
        Livewire::test(DepartureStandFinder::class)
            ->fireEvent('departureStandFinderFormSubmitted', [
                'stand' => [
                    'identifier' => '123',
                    'airfield' => 'EGXY',
                    'terminal' => null,
                    'type' => null,
                    'aerodrome_reference_code' => 'C',
                    'max_aircraft_wingspan' => null,
                    'max_aircraft_length' => null,
                ],
                'occupancy' => [
                    'airfield' => 'EGXY',
                    'occupied' => 2,
                    'total' => 3,
                    'percentage' => 67,
                ],
            ])
            ->assertSee('Stand 123')
            ->assertSee('Occupancy')
            ->assertSee('fi-color-warning')
            ->assertSeeHtmlInOrder(['Stand 123', '2/3 (67%)']);
    }

    public function testItShowsOccupancyWhenNoStandIsFound()
    {
        Livewire::test(DepartureStandFinder::class)
            ->fireEvent('departureStandFinderFormSubmitted', [
                'error' => 'No available stand found at EGXY that fits the B73X.',
                'occupancy' => [
                    'airfield' => 'EGXY',
                    'occupied' => 1,
                    'total' => 1,
                    'percentage' => 100,
                ],
            ])
            ->assertSee('fi-color-danger')
            ->assertSeeHtmlInOrder([
                'No available stand found at EGXY that fits the B73X.',
                '1/1 (100%)',
            ]);
    }

    public function testItColorsLowOccupancyAsSuccess()
    {
        Livewire::test(DepartureStandFinder::class)
            ->fireEvent('departureStandFinderFormSubmitted', [
                'stand' => [
                    'identifier' => '123',
                    'airfield' => 'EGXY',
                    'terminal' => null,
                    'type' => null,
                    'aerodrome_reference_code' => 'C',
                    'max_aircraft_wingspan' => null,
                    'max_aircraft_length' => null,
                ],
                'occupancy' => [
                    'airfield' => 'EGXY',
                    'occupied' => 0,
                    'total' => 2,
                    'percentage' => 0,
                ],
            ])
            ->assertSee('fi-color-success')
            ->assertSee('0/2 (0%)');
    }
}
