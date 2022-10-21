<?php

namespace App\Filament\Helpers;

use App\BaseFilamentTestCase;
use Livewire\Livewire;

class HasSquawkRangesTest extends BaseFilamentTestCase
{
    /**
     * @dataProvider coordinateProvider
     */
    public function testItHasSquawkRanges(string $first, string $last)
    {
        Livewire::test(CreateFakeSquawkRange::class)
            ->set('data.first', $first)
            ->set('data.last', $last)
            ->call('create')
            ->assertHasNoErrors();
    }

    public function coordinateProvider(): array
    {
        return [
            'Low' => ['0001', '0005'],
            'Low Crossing Tens' => ['0001', '0312'],
            'Mid range' => ['2324', '4243'],
            'Mid range 2' => ['5241', '5777'],
            'High range' => ['6666', '6771'],
            'High range 2' => ['7571', '7763'],
        ];
    }

    /**
     * @dataProvider badCoordinateProvider
     */
    public function testItHasErrorsOnBadSquawks(string|null $first, string|null $last, array $expectedErrors)
    {
        Livewire::test(CreateFakeSquawkRange::class)
            ->set('data.first', $first)
            ->set('data.longitude', $last)
            ->call('create')
            ->assertHasErrors($expectedErrors);
    }

    public function badCoordinateProvider(): array
    {
        return [
            'First null' => [null, '2312', ['data.first']],
            'Last null' => ['2114', null, ['data.last']],
            'First not valid squawk' => ['2218', '2241', ['data.first']],
            'First not numeric' => ['a231', '2241', ['data.first']],
            'Last not valid squawk' => ['2231', '2288', ['data.last']],
            'Last not numeric' => ['2231', '237a', ['data.last']],
            'Both bad' => ['aaaa', '237a', ['data.first', 'data.last']],
        ];
    }
}
