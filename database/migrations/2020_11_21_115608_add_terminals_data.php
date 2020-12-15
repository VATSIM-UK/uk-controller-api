<?php

use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use Illuminate\Database\Migrations\Migration;

class AddTerminalsData extends Migration
{
    const TERMINALS_FILE = __DIR__ . '/../data/stands/2020/terminals.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $terminals = fopen(self::TERMINALS_FILE, 'r');
        while ($line = fgetcsv($terminals)) {
            Terminal::create(
                [
                    'airfield_id' => Airfield::where('code', $line[0])->first()->id,
                    'key' => $line[1],
                    'description' => $line[2],
                ]
            );
        }
        fclose($terminals);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return - if this fails we'll probably be dropping table anyway.
    }
}
