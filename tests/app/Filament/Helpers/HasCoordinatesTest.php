<?php

namespace App\Filament\Helpers;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AirfieldResource\Pages\EditAirfield;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class HasCoordinatesTest extends BaseFilamentTestCase
{
    #[DataProvider('coordinateProvider')]
    public function testItHasCoordinates(float $latitude, float $longitude)
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.latitude', $latitude)
            ->set('data.longitude', $longitude)
            ->call('save')
            ->assertHasNoErrors();
    }

    public static function coordinateProvider(): array
    {
        return [
            'Normal' => [1.23, 4.56],
            'Negative latitude' => [-12.321, 4.56],
            'Negative latitude above' => [-89.999, 4.56],
            'Negative latitude boundary' => [-90.00, 4.56],
            'Positive latitude' => [22.531, 4.56],
            'Positive latitude below' => [89.9999, 4.56],
            'Positive latitude boundary' => [90.000, 4.56],
            'Negative longitude' => [1.23, -12.5342],
            'Negative longitude above' => [1.23, -179.999],
            'Negative longitude boundary' => [1.23, -180.0],
            'Positive longitude' => [1.23, 12.5342],
            'Positive longitude above' => [1.23, 179.999],
            'Positive longitude boundary' => [1.23, 180.0],
        ];
    }

    #[DataProvider('badCoordinateProvider')]
    public function testItHasErrorsOnBadCoordinates(float|null $latitude, float|null $longitude, array $expectedErrors)
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.latitude', $latitude)
            ->set('data.longitude', $longitude)
            ->call('save')
            ->assertHasErrors($expectedErrors);
    }

    public static function badCoordinateProvider(): array
    {
        return [
            'Latitude null' => [null, 4.56, ['data.latitude']],
            'Longitude null' => [1.23, null, ['data.longitude']],
            'Latitude too small' => [-90.1, 4.56, ['data.latitude']],
            'Latitude too big' => [90.1, 4.56, ['data.latitude']],
            'Longitude too small' => [1.23, -180.001, ['data.longitude']],
            'Longitude too big' => [1.23, 180.001, ['data.longitude']],
        ];
    }
}
