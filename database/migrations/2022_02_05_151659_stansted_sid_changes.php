<?php

use App\Models\Sid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class StanstedSidChanges extends Migration
{
    const CHANGES = [
        'CLN4S' => 'CLN5S',
        'CLN1E' => 'CLN2E',
        'CLN8R' => 'CLN9R',
        'DET1D' => 'DET2D',
        'DET1S' => 'DET2S',
        'DET1R' => 'DET2R',
        'LAM2S' => 'LAM3S',
        'LAM3R' => 'LAM4R',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::CHANGES as $oldIdentifer => $newIdentifier) {
            Sid::whereHas('runway.airfield', function (Builder $airfield) {
                $airfield->where('code', 'EGSS');
            })
                ->where('identifier', $oldIdentifer)
                ->firstOrFail()
                ->update(['identifier', $newIdentifier]);
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
