<?php

namespace App\Imports\Srd;

use App\Models\Srd\SrdNote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class SrdNotesImport implements WithEvents, ToCollection
{
    use Importable;

    const NEW_ROW_REGEX = '/^Note (\d+)$/';

    public function collection(Collection $rows): void
    {
        $foundFirst = false;
        $row = 0;
        $this->output->progressStart($rows->count());
        while ($row < $rows->count()) {

            // Find the first one
            if (!$foundFirst) {
                if (preg_match(self::NEW_ROW_REGEX, $rows[$row][0])) {
                    $foundFirst = true;
                    continue;
                }
                $row++;
                continue;
            }

            // Get the note id, process a row and then increment the counter by rows processed (+1 for the header line)
            $matchArray = [];
            preg_match(self::NEW_ROW_REGEX, $rows[$row][0], $matchArray);
            $rowBefore = $row;
            $row += $this->processNote($rows, ++$row, (int)$matchArray[1]);
            $this->output->progressAdvance($row - $rowBefore);
        }
        $this->output->progressFinish();
    }

    private function processNote(Collection $rows, int $row, int $noteId): int
    {
        $startRow = $row;
        $rowText = '';
        while ($row < $rows->count()) {

            // If we find a new note, save the current
            if (preg_match(self::NEW_ROW_REGEX, $rows[$row][0])) {
                break;
            }

            $rowText .= $rows[$row][0] . PHP_EOL;
            $row++;
        }

        SrdNote::create(
            [
                'id' => $noteId,
                'note_text' => trim($rowText),
            ]
        );

        return $row - $startRow;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function () {
                $this->output->section('Importing SRD Notes');
            },
        ];
    }
}
