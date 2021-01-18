<?php

namespace App\Imports\Wake;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\RecatCategory;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class RecatImporter implements ToCollection
{
    use Importable;

    public function collection(Collection $rows): void
    {
        $categories = RecatCategory::all()->mapWithKeys(function (RecatCategory $category) {
            return [$category['code'] => $category['id']];
        })->toArray();

        $this->output->progressStart($rows->count());
        foreach ($rows as $row) {
            if (!isset($row[0], $row[1])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid RECAT import data: %s', is_array($row) ? implode(',', $row) : 'Not an array')
                );
            }

            if (!array_key_exists($row[1], $categories)) {
                throw new InvalidArgumentException(
                    sprintf('RECAT category not found: %s', is_array($row) ? implode(',', $row) : 'Not an array')
                );
            }

            Aircraft::where('code', $row[0])->update(['recat_category_id' => $categories[$row[1]]]);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
