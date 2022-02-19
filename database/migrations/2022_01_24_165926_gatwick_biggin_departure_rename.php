<?php

use App\Models\Sid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class GatwickBigginDepartureRename extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Sid::whereIn('identifier', ['BIG26L', 'BIG26R', 'BIG08R', 'BIG08L'])
            ->whereHas('runway.airfield', function (Builder $airfield) {
                $airfield->where('code', 'EGKK');
            })
            ->get()
            ->each(function (Sid $sid) {
                $newSid = $sid->replicate();
                $newSid->identifier = 'BIG';
                $newSid->save();
            });
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
