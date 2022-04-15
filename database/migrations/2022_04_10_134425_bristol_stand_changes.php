<?php

use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class BristolStandChanges extends Migration
{
    const STANDS_TO_CLOSE = [
        '32L',
        '32R',
        '32',
        '3R',
        '7N',
    ];

    const STANDS_TO_RENAME = [
        'W8E' => '8',
        'W9E' => '9',
        'W10E' => '10',
        'W11E' => '11',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Close unused stands
        Stand::whereIn('identifier', self::STANDS_TO_CLOSE)
            ->airfield('EGGD')
            ->get()
            ->each(function (Stand $stand) {
                $stand->close();
            });

        // Rename stands
        foreach (self::STANDS_TO_RENAME as $oldIdentifier => $newIdentifier) {
            Stand::where('identifier', $oldIdentifier)
                ->airfield('EGGD')
                ->update(['identifier' => $newIdentifier]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
