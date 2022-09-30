<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\Handoff;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Airfield::whereNull('handoff_id')
            ->get()
            ->each(function (Airfield $airfield) {
                $handoff = Handoff::create(
                    ['description' => sprintf('Default departure handoff for %s', $airfield->code)]
                );
                $airfield->update(['handoff_id' => $handoff->id]);
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
};
