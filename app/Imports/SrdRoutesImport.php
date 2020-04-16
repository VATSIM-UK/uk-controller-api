<?php

namespace App\Imports;

use App\Models\Srd\SrdRoute;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SrdRoutesImport implements ToModel, WithStartRow
{
    const FL_CONVERSION_FACTOR = 100;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SrdRoute([
            'origin' => $row[0],
            'destination' => $row[6],
            'min_level' => $this->convertFlightLevel($row[2] === 'MC' ? 0 : $row[2]),
            'max_level' => $this->convertFlightLevel($row[3]),
            'route_segment' => $row[4] ?? '',
            'sid' => $row[1],
            'star' => $row[5],
        ]);
    }

    private function convertFlightLevel($flightLevel): int
    {
        return ((int) $flightLevel) * self::FL_CONVERSION_FACTOR;
    }

    public function startRow(): int
    {
        return 2;
    }
}
