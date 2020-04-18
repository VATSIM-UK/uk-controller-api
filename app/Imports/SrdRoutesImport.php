<?php

namespace App\Imports;

use App\Models\Srd\SrdRoute;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeSheet;

class SrdRoutesImport implements ToCollection, WithStartRow, WithProgressBar, WithEvents
{
    use Importable;

    const FL_CONVERSION_FACTOR = 100;
    const FL_MIN_CRUISE = 'MC';

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $route = SrdRoute::create([
                'origin' => $row[0],
                'destination' => $row[6],
                'minimum_level' => $this->convertFlightLevel($row[2]),
                'maximum_level' => $this->convertFlightLevel($row[3]),
                'route_segment' => $row[4] ?? '',
                'sid' => $row[1],
                'star' => $row[5],
            ]);
        }
    }

    private function convertFlightLevel(string $flightLevel): ?int
    {
        return $flightLevel === self::FL_MIN_CRUISE ? null : ((int) $flightLevel) * self::FL_CONVERSION_FACTOR;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $sheet) {
                $this->output->section('Importing SRD Routes');
            },
        ];
    }
}
