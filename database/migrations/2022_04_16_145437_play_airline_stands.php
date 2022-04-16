<?php

use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

class PlayAirlineStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Airline::where('icao_code', 'FPY')
            ->firstOrFail()
            ->stands()
            ->attach($this->getStandIds());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Airline::where('icao_code', 'FPY')
            ->firstOrFail()
            ->stands()
            ->detach($this->getStandIds());
    }

    private function getStandIds(): Collection
    {
        return Stand::whereIn('identifier', ['40', '41', '42', '42L', '42R', '43', '43L', '43R', '44', '44L', '44R'])
            ->airfield('EGSS')
            ->pluck('id');
    }
}
