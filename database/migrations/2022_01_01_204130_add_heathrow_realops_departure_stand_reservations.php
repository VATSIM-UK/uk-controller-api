<?php

use App\Models\Stand\Stand;
use App\Services\Stand\StandReservationService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddHeathrowRealopsDepartureStandReservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $reservationFile = fopen(__DIR__ . '/../data/stands/heathrow-realops-2022/realops-departures.csv', 'r+');
        DB::transaction(function () use ($reservationFile) {
            while ($reservation = fgetcsv($reservationFile)) {
                dump($reservation[0], $reservation[6]);
                StandReservationService::createStandReservation(
                    $reservation[0],
                    Stand::where('identifier', $reservation[6])->airfield('EGLL')->firstOrFail()->id,
                    Carbon::parse(sprintf('2022-01-09 %s:00', $reservation[4]))->subMinutes(35),
                    Carbon::parse(sprintf('2022-01-09 %s:00', $reservation[4])),
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
