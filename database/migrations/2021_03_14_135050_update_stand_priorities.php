<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStandPriorities extends Migration
{
    const STANDS = [
        'EGKK' => [
            // Not connected to any terminals
            50 => [
                '130',
                '131',
                '132',
                '133',
                '134',
                '135',
                '136',
                '140',
                '141L',
                '141',
                '141R',
                '142',
                '142R',
                '143L',
                '143',
                '143R',
                '144L',
                '144',
                '144R',
                '145',
            ],
            // These are a bit out of the way
            25 => [
                '37',
                '38',
                '574',
                '573',
                '572',
                '571',
                '570',
                '569',
                '568',
                '567',
                '566',
                '565',
            ],
        ],
        'EGLL' => [
            // All of EGLL's T5 stands are low priority by default as they're for BA
            125 => [
                '524',
                '525',
                '526',
                '527'
            ],
        ],
        'EGLC' => [
            25 => [
                '10',
                '12',
                '13',
                '14',
            ],
            100 => [
                '15',
            ],
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::STANDS as $airfield => $standPriorities) {
            $airfieldId = DB::table('airfield')->where('code', $airfield)->first()->id;
            foreach ($standPriorities as $priority => $stands) {
                DB::table('stands')
                    ->where('airfield_id', $airfieldId)
                    ->whereIn('identifier', $stands)
                    ->update(
                    [
                        'assignment_priority' => $priority,
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
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
