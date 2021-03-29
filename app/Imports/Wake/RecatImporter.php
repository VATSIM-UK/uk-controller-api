<?php

namespace App\Imports\Wake;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class RecatImporter implements ToCollection
{
    use Importable;

    public function collection(Collection $rows): void
    {
        $categories = WakeCategory::whereHas(
            'scheme',
            function (Builder $scheme) {
                return $scheme->recat();
            }
        )->get()->mapWithKeys(
            function (WakeCategory $category) {
                return [$category['code'] => $category['id']];
            }
        )->toArray();

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

            if ($aircraft = Aircraft::where('code', $row[0])->first()) {
                $categoriesToKeep = $aircraft->wakeCategories->filter(function (WakeCategory $category) {
                    return !$category->scheme->isRecat();
                })->pluck('id')->toArray();

                $aircraft->wakeCategories()->sync(array_merge($categoriesToKeep, [$categories[$row[1]]]));
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
