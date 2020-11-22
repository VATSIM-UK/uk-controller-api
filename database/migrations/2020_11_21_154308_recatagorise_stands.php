<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use Illuminate\Database\Migrations\Migration;

class RecatagoriseStands extends Migration
{
    private const STAND_DATA_CSV = __DIR__ . '/../data/stands/2020/standcats.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $wakeCategories = WakeCategory::all()->mapWithKeys(function (WakeCategory $category) {
            return [$category->code => $category->id];
        });

        $airfields = Airfield::all()->mapWithKeys(function (Airfield $airfield) {
            return [$airfield->code => $airfield->id];
        });

        $file = fopen(self::STAND_DATA_CSV, 'r');
        while (($line = fgetcsv($file))) {
            if (!isset($airfields[$line[0]])) {
                throw new InvalidArgumentException('Invalid airfield ' . $line[0]);
            }

            $airfieldId = $airfields[$line[0]];

            if (!isset($wakeCategories[$line[3]])) {
                throw new InvalidArgumentException('Invalid wake category ' . $line[3]);
            }

            $wakeCategoryId = $wakeCategories[$line[3]];

            if(!in_array($line[2], ['DOMESTIC', 'INTERNATIONAL', 'CARGO', ''])) {
                throw new InvalidArgumentException('Invalid stand type ' . $line[2]);
            }

            $standType = StandType::where('key', $line[2])->first();
            $standTypeId = $standType ? $standType->id : null;

            if (!in_array($line[4], ['0', '1'])) {
               throw new InvalidArgumentException('Invalid general use value ' . $line[4]);
            }

            $generalUse = $line[4] === '1';

            $terminalId = null;
            if (!empty($line[5])) {
                $terminal = Terminal::where('key', $line[5])->first();
                if (!$terminal) {
                    throw new InvalidArgumentException('Invalid terminal ' . $line[5]);
                }
                $terminalId = $terminal->id;
            }

            $stand = Stand::where('airfield_id', $airfieldId)
                ->where('identifier', $line[1])
                ->first();

            if (!$stand) {
                throw new InvalidArgumentException(sprintf('Invalid stand %s/%s', $line[0], $line[1]));
            }

            $stand->update(
                [
                    'wake_category_id' => $wakeCategoryId,
                    'type_id' => $standTypeId,
                    'terminal_id' => $terminalId,
                    'general_use' => $generalUse,
                ]
            );
        }
        fclose($file);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return.
    }
}
