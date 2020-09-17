<?php

namespace App\Imports\Srd;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SrdImport implements WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    public function sheets(): array
    {
        return [
            'Notes' => (new SrdNotesImport())->withOutput($this->output),
            'Routes' => (new SrdRoutesImport())->withOutput($this->output),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Nothing to do
    }
}
