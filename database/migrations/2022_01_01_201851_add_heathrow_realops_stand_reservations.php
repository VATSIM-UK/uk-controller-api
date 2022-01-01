<?php

use App\Models\Stand\Stand;
use App\Services\Stand\StandReservationService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHeathrowRealopsStandReservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $reservationFile = fopen(__DIR__ . '/../data/stands/heathrow-realops-2022/realops-arrivals.csv', 'r+');
        DB::transaction(function () use ($reservationFile) {
            while ($reservation = fgetcsv($reservationFile)) {
                StandReservationService::createStandReservation(
                    $reservation[0],
                    Stand::where('identifier', $reservation[7])->airfield('EGLL')->firstOrFail()->id,
                    Carbon::parse(sprintf('2022-01-09 %s:00', $reservation[5]))->subMinutes(35),
                    Carbon::parse(sprintf('2022-01-09 %s:00', $reservation[5])),
                    $reservation[1],
                    $reservation[2]
                );
            }
        });
        fclose($reservationFile);
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
