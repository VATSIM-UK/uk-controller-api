<?php

use App\Models\Controller\Handoff;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Handoff::insert($this->getHandoffData());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Handoff::truncate();
    }

    private function getHandoffData(): array
    {
        return [
            [
                'key' => 'EGKK_SID_EAST',
                'description' => 'Gatwick Eastbound Departures (Except BIG)',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGKK_SID_BIG',
                'description' => 'Gatwick Biggin Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGKK_SID_WEST',
                'description' => 'Gatwick Westbound and Southbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGKK_SID_SFD_08',
                'description' => 'Gatwick SFD Departures on Easterlies',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLL_SID_SOUTH_EAST',
                'description' => 'Heathrow South-eastbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLL_SID_SOUTH_WEST',
                'description' => 'Heathrow South-eastbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLL_SID_NORTH_WEST',
                'description' => 'Heathrow North-westbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLL_SID_NORTH_EAST',
                'description' => 'Heathrow North-eastbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLL_SID_CPT_09',
                'description' => 'Heathrow CPT Departures on Easterlies',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLC_SID_SOUTH',
                'description' => 'London City Southbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLC_SID_CLN',
                'description' => 'London City Clacton Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLC_SID_BPK_CPT_09',
                'description' => 'London City Brookmans Park / Compton Departures on 09',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLC_SID_BPK_CPT_27',
                'description' => 'London City Brookmans Park / Compton Departures on 27',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGSS_SID_WEST',
                'description' => 'London Stansted Westbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGSS_SID_EAST_SOUTH',
                'description' => 'London Stansted South and Eastbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGW_SID_SOUTH_EAST',
                'description' => 'London Luton South and Eastbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGW_SID_WEST_08',
                'description' => 'London Luton Westbound Departures on 08',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGW_SID_WEST_26',
                'description' => 'London Luton Westbound Departures on 26',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGCC_SID_EAST_NORTH',
                'description' => 'Manchester East and Northbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGCC_SID_WEST',
                'description' => 'Manchester Westbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGP_SID',
                'description' => 'Liverpool Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNM_SID',
                'description' => 'Leeds Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGCN_SID',
                'description' => 'Doncaster Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGJJ_SID',
                'description' => 'Jersey Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGJB_SID',
                'description' => 'Guernsey Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGD_SID_27_BADIM',
                'description' => 'Bristol BADIM Departures on 27',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGD_SID_27',
                'description' => 'Bristol Departures on 27',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGGD_SID_09',
                'description' => 'Bristol Departures on 09',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGFF_SID',
                'description' => 'Cardiff Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGBB_SID',
                'description' => 'Birmingham Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNX_SID_NORTH_09',
                'description' => 'East Midlands Northbound Departures on 09',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNX_SID_SOUTH_09',
                'description' => 'East Midlands Southbound Departures on 09',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNX_SID_NORTH_27',
                'description' => 'East Midlands Northbound Departures on 27',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNX_SID_SOUTH_27',
                'description' => 'East Midlands Southbound Departures on 27',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPH_SID_SOUTH',
                'description' => 'Edinburgh Southbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPH_SID_NORTH',
                'description' => 'Edinburgh Northbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPF_SID_SOUTH',
                'description' => 'Glasgow Southbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPF_SID_NORTH',
                'description' => 'Glasgow Northbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPF_SID_WEST',
                'description' => 'Glasgow Westbound Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPK_SID',
                'description' => 'Prestwick Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGNT_SID',
                'description' => 'Newcastle Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGWU_SID',
                'description' => 'Northolt Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGPD_DEPARTURE',
                'description' => 'Aberdeen Departures',
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
