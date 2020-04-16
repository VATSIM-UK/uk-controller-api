<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SrdImport implements WithProgressBar, WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    public function sheets(): array
    {
        return [
            'Routes' => new SrdRoutesImport(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Nothing to do
    }
}
