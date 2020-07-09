<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddScottishFlightInformationRegionData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $firId = DB::table('flight_information_regions')
            ->insertGetId(
                [
                    'identification_code' => 'EGPX',
                    'created_at' => Carbon::now(),
                ]
            );

        DB::table('flight_information_region_boundaries')->insert(
            [
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N061.00.00.000',
                    'start_longitude' => 'W010.00.00.000',
                    'finish_latitude' => 'N061.00.00.000',
                    'finish_longitude' => 'E000.00.00.000',
                    'description' => 'RATSU, MATIK, NALAN, OSBON, GONUT, PEMOS, RIXUN, SOSAR, GUNPA',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N061.00.00.000',
                    'start_longitude' => 'E000.00.00.000',
                    'finish_latitude' => 'N059.59.31.000',
                    'finish_longitude' => 'E000.00.54.000',
                    'description' => 'GUNPA, PEPIN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N059.59.31.000',
                    'start_longitude' => 'E000.00.54.000',
                    'finish_latitude' => 'N059.38.18.000',
                    'finish_longitude' => 'E000.40.09.000',
                    'description' => 'PEPIN, ORVIK',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N059.38.18.000',
                    'start_longitude' => 'E000.40.09.000',
                    'finish_latitude' => 'N059.02.09.000',
                    'finish_longitude' => 'E001.44.20.000',
                    'description' => 'ORVIK, BEREP',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N059.02.09.000',
                    'start_longitude' => 'E001.44.20.000',
                    'finish_latitude' => 'N058.47.36.000',
                    'finish_longitude' => 'E002.09.18.000',
                    'description' => 'BEREP, RIGVU',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N058.47.36.000',
                    'start_longitude' => 'E002.09.18.000',
                    'finish_latitude' => 'N058.23.24.000',
                    'finish_longitude' => 'E002.49.44.000',
                    'description' => 'RIGVU, KLONN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N058.23.24.000',
                    'start_longitude' => 'E002.49.44.000',
                    'finish_latitude' => 'N057.54.27.000',
                    'finish_longitude' => 'E003.36.30.000',
                    'description' => 'KLONN, ALOTI',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N057.54.27.000',
                    'start_longitude' => 'E003.36.30.000',
                    'finish_latitude' => 'N057.34.12.000',
                    'finish_longitude' => 'E004.08.13.000',
                    'description' => 'ALOTI, NIVUN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N057.34.12.000',
                    'start_longitude' => 'E004.08.13.000',
                    'finish_latitude' => 'N057.06.36.000',
                    'finish_longitude' => 'E004.50.11.000',
                    'description' => 'NIVUN, PENUN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N057.06.36.000',
                    'start_longitude' => 'E004.50.11.000',
                    'finish_latitude' => 'N057.00.00.000',
                    'finish_longitude' => 'E005.00.00.000',
                    'description' => 'PENUN, ATNAK',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N057.00.00.000',
                    'start_longitude' => 'E005.00.00.000',
                    'finish_latitude' => 'N055.07.43.000',
                    'finish_longitude' => 'E005.00.00.000',
                    'description' => 'ATNAK, VAXIT, TINAC, GOREV, PETIL, INBOB, LESRA, SOPTO, UPGAS, VALBO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N055.07.43.000',
                    'start_longitude' => 'E005.00.00.000',
                    'finish_latitude' => 'N055.00.00.000',
                    'finish_longitude' => 'E005.00.00.000',
                    'description' => 'VALBO to FIR corner',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N055.00.00.000',
                    'start_longitude' => 'E005.00.00.000',
                    'finish_latitude' => 'N055.00.00.000',
                    'finish_longitude' => 'W005.30.00.000',
                    'description' => 'Bottom of FIR',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N055.00.00.000',
                    'start_longitude' => 'W005.30.00.000',
                    'finish_latitude' => 'N053.55.00.000',
                    'finish_longitude' => 'W005.30.00.000',
                    'description' => 'North south line near Aldergrove to corner with EISN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N053.55.00.000',
                    'start_longitude' => 'W005.30.00.000',
                    'finish_latitude' => 'N053.57.54.000',
                    'finish_longitude' => 'W005.44.32.000',
                    'description' => 'Corner with EISN to NIMAT',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N053.57.54.000',
                    'start_longitude' => 'W005.44.32.000',
                    'finish_latitude' => 'N054.25.00.000',
                    'finish_longitude' => 'W008.10.00.000',
                    'description' => 'NIMAT, ROTEV, NEVRI, BAMLI, DEGOS, NESON, ERNAN',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N054.25.00.000',
                    'start_longitude' => 'W008.10.00.000',
                    'finish_latitude' => 'N054.38.58.000',
                    'finish_longitude' => 'W009.33.20.000',
                    'description' => 'NIPIT, MOLAK',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N054.38.58.000',
                    'start_longitude' => 'W009.33.20.000',
                    'finish_latitude' => 'N054.34.00.000',
                    'finish_longitude' => 'W010.00.00.000',
                    'description' => 'NOTA southwest corner',
                    'created_at' => Carbon::now(),
                ],
                [
                    'flight_information_region_id' => $firId,
                    'start_latitude' => 'N054.34.00.000',
                    'start_longitude' => 'W010.00.00.000',
                    'finish_latitude' => 'N061.00.00.000',
                    'finish_longitude' => 'W010.00.00.000',
                    'description' =>
                        'NIBOG, LUTOV, KUGUR, APSOV, MIMKU, AMLAD, IBROD, GOMUP, ETILO, ERAKA, ADODO, BALIX, ATSIX, LUSEN, RATSU',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('flight_information_regions')->where('identification_code', 'EGPX')->delete();
    }
}
