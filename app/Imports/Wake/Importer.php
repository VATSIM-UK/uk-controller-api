<?php

namespace App\Imports\Wake;

use App\Exceptions\InvalidWakeImportException;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Importer implements WithHeadingRow, ToCollection
{
    use Importable;

    const TYPE_DESIGNATOR_COLUMN = 'icao_type_designator';
    const WAKE_CATEGORY_COLUMN = 'uk_arrival_wtc';

    const WAKE_CATEGORY_MAP = [
        'LIGHT' => 'L',
        'SMALL' => 'S',
        'LOWER MEDIUM' => 'LM',
        'UPPER MEDIUM' => 'UM',
        'HEAVY' => 'H',
        'SUPER' => 'J',
    ];

    public function collection(Collection $rows): void
    {
        $this->output->progressStart($rows->count());
        foreach ($rows as $row) {

            // Check for the data
            if (!isset($row[self::WAKE_CATEGORY_COLUMN], $row[self::TYPE_DESIGNATOR_COLUMN])) {
                throw new InvalidWakeImportException('Invalid row format');
            }

            // Get the wake category
            if (($wakeCategory = $this->getWakeCategory($row[self::WAKE_CATEGORY_COLUMN])) === null) {
                $this->output->warning(
                    'Invalid UK wake category for aircraft type ' . $row[self::TYPE_DESIGNATOR_COLUMN]
                );
                $this->output->progressAdvance();
                continue;
            }

            // Perform updates
            Aircraft::updateOrCreate(
                [
                    'code' => $row[self::TYPE_DESIGNATOR_COLUMN],
                ],
                [
                    'wake_category_id' => $wakeCategory->id,
                ]
            );
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function getWakeCategory(?string $categoryFromDocument): ?WakeCategory
    {
        if (
            $categoryFromDocument === null ||
            !array_key_exists($categoryFromDocument, self::WAKE_CATEGORY_MAP)
        ) {
            return null;
        }

        return WakeCategory::where('code', self::WAKE_CATEGORY_MAP[$categoryFromDocument])->first();
    }
}
