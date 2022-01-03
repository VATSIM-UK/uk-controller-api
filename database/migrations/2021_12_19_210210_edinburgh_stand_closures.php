<?php

use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class EdinburghStandClosures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->apply('close');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->apply('open');
    }

    private function apply(string $function)
    {
        Stand::whereIn('identifier', ['9A', '10A', '209'])
            ->airfield('EGPH')
            ->get()
            ->each(function (Stand $stand) use ($function) {
                $stand->$function();
            });
    }
}
