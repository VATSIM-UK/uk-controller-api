<?php

use App\Models\Airfield\Airfield;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirfieldsToAsrs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airfields = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$airfields) {
            $airfields[$airfield->code] = $airfield->id;
        });

        $asrs = [];
        AltimeterSettingRegion::all()->each(function (AltimeterSettingRegion $asr) use (&$asrs) {
            $asrs[$asr->key] = $asr->id;
        });
        DB::table('altimeter_setting_region_airfield')->insert($this->getAsrAirfieldData($asrs, $airfields));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('altimeter_setting_region_airfield')->truncate();
    }

    public function getAsrAirfieldData(array $asrs, array $airfields) : array
    {
        return [
            // TMAS
            [
                'altimeter_setting_region_id' => $asrs['ASR_LONDON'],
                'airfield_id' => $airfields['EGLL'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_MANCHESTER'],
                'airfield_id' => $airfields['EGCC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SCOTTISH'],
                'airfield_id' => $airfields['EGPF'],
            ],

            // Skerry
            [
                'altimeter_setting_region_id' => $asrs['ASR_SKERRY'],
                'airfield_id' => $airfields['EGPC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SKERRY'],
                'airfield_id' => $airfields['EGPA'],
            ],

            // Portree
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTREE'],
                'airfield_id' => $airfields['EGEO'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTREE'],
                'airfield_id' => $airfields['EGPU'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTREE'],
                'airfield_id' => $airfields['EGPR'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTREE'],
                'airfield_id' => $airfields['EGPL'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTREE'],
                'airfield_id' => $airfields['EGPO'],
            ],

            // Rattray
            [
                'altimeter_setting_region_id' => $asrs['ASR_RATTRAY'],
                'airfield_id' => $airfields['EGPD'],
            ],

            // Tyne
            [
                'altimeter_setting_region_id' => $asrs['ASR_TYNE'],
                'airfield_id' => $airfields['EGNT'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_TYNE'],
                'airfield_id' => $airfields['EGNV'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_TYNE'],
                'airfield_id' => $airfields['EGQM'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_TYNE'],
                'airfield_id' => $airfields['EGOM'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_TYNE'],
                'airfield_id' => $airfields['EGNC'],
            ],

            // Belfast
            [
                'altimeter_setting_region_id' => $asrs['ASR_BELFAST'],
                'airfield_id' => $airfields['EGAA'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BELFAST'],
                'airfield_id' => $airfields['EGAC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BELFAST'],
                'airfield_id' => $airfields['EGAE'],
            ],

            // Holyhead
            [
                'altimeter_setting_region_id' => $asrs['ASR_HOLYHEAD'],
                'airfield_id' => $airfields['EGOV'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HOLYHEAD'],
                'airfield_id' => $airfields['EGCK'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HOLYHEAD'],
                'airfield_id' => $airfields['EGNS'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HOLYHEAD'],
                'airfield_id' => $airfields['EGOY'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HOLYHEAD'],
                'airfield_id' => $airfields['EGCW'],
            ],

            // Barnsley
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGBE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGBB'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGNX'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGNM'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGCN'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGWC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGBO'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGOS'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGXE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGXC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGXG'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGNE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGYD'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_BARNSLEY'],
                'airfield_id' => $airfields['EGNJ'],
            ],

            // Humber
            [
                'altimeter_setting_region_id' => $asrs['ASR_HUMBER'],
                'airfield_id' => $airfields['EGNJ'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HUMBER'],
                'airfield_id' => $airfields['EGXV'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_HUMBER'],
                'airfield_id' => $airfields['EGXC'],
            ],

            // Scillies
            [
                'altimeter_setting_region_id' => $asrs['ASR_SCILLIES'],
                'airfield_id' => $airfields['EGDR'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SCILLIES'],
                'airfield_id' => $airfields['EGHE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SCILLIES'],
                'airfield_id' => $airfields['EGHT'],
            ],

            // Wessex
            [
                'altimeter_setting_region_id' => $asrs['ASR_WESSEX'],
                'airfield_id' => $airfields['EGHQ'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_WESSEX'],
                'airfield_id' => $airfields['EGTE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_WESSEX'],
                'airfield_id' => $airfields['EGFE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_WESSEX'],
                'airfield_id' => $airfields['EGFH'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_WESSEX'],
                'airfield_id' => $airfields['EGHC'],
            ],

            // Chatham
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGKA'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGHR'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGMD'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGVO'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGMC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGTO'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGKB'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGLC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGLL'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGWU'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGSC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGSS'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_CHATHAM'],
                'airfield_id' => $airfields['EGGW'],
            ],

            // Portland
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTLAND'],
                'airfield_id' => $airfields['EGHI'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTLAND'],
                'airfield_id' => $airfields['EGHH'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTLAND'],
                'airfield_id' => $airfields['EGTE'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTLAND'],
                'airfield_id' => $airfields['EGDY'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PORTLAND'],
                'airfield_id' => $airfields['EGDX'],
            ],

            // Yarmouth
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGSH'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGYM'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGXH'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGUL'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGUN'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_YARMOUTH'],
                'airfield_id' => $airfields['EGYC'],
            ],

            // Cotswold
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGGD'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGFF'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGBP'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGBJ'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGVN'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGTK'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_COTSWOLD'],
                'airfield_id' => $airfields['EGTC'],
            ],

            // Shetland
            [
                'altimeter_setting_region_id' => $asrs['ASR_SHETLAND'],
                'airfield_id' => $airfields['EGPB'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SHETLAND'],
                'airfield_id' => $airfields['EGPM'],
            ],

            // Orkney
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGPD'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGPS'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGOS'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGQK'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGPC'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_ORKNEY'],
                'airfield_id' => $airfields['EGPE'],
            ],

            // Marlin
            [
                'altimeter_setting_region_id' => $asrs['ASR_MARLIN'],
                'airfield_id' => $airfields['EGPB'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_MARLIN'],
                'airfield_id' => $airfields['EGPM'],
            ],

            // Petrel
            [
                'altimeter_setting_region_id' => $asrs['ASR_PETREL'],
                'airfield_id' => $airfields['EGPD'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PETREL'],
                'airfield_id' => $airfields['EGQL'],
            ],

            // Skua
            [
                'altimeter_setting_region_id' => $asrs['ASR_SKUA'],
                'airfield_id' => $airfields['EGNT'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SKUA'],
                'airfield_id' => $airfields['EGNV'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_SKUA'],
                'airfield_id' => $airfields['EGPD'],
            ],

            // Puffin
            [
                'altimeter_setting_region_id' => $asrs['ASR_PUFFIN'],
                'airfield_id' => $airfields['EGPB'],
            ],
            [
                'altimeter_setting_region_id' => $asrs['ASR_PUFFIN'],
                'airfield_id' => $airfields['EGPM'],
            ],
        ];
    }
}
