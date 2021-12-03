<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAirfieldElevations extends Migration
{
    const AIRFIELD_MAP = [
        'EGPD' => 215,
        'EGJA' => 290,
        'EGSL' => 286,
        'EGPR' => 5,
        'EGNL' => 44,
        'EGBF' => 273,
        'EGAA' => 268,
        'EGAC' => 15,
        'EGPL' => 189,
        'EGKB' => 599,
        'EGBB' => 339,
        'EGLK' => 325,
        'EGNH' => 34,
        'EGHH' => 38,
        'EGGD' => 622,
        'EGCK' => 14,
        'EGSC' => 48,
        'EGEC' => 42,
        'EGFF' => 220,
        'EGNC' => 190,
        'EGLJ' => 240,
        'EGHR' => 108,
        'EGEL' => 21,
        'EGEY' => 44,
        'EGHA' => 811,
        'EGBE' => 267,
        'EGTC' => 359,
        'EGPG' => 350,
        'EGLD' => 249,
        'EGBD' => 175,
        'EGCN' => 54,
        'EGPN' => 17,
        'EGTU' => 839,
        'EGSU' => 126,
        'EGSR' => 227,
        'EGNX' => 306,
        'EGED' => 13,
        'EGPH' => 136,
        'EGTR' => 332,
        'EGAB' => 155,
        'EGTE' => 102,
        'EGEF' => 237,
        'EGTF' => 80,
        'EGLF' => 238,
        'EGCL' => 6,
        'EGPF' => 26,
        'EGBJ' => 101,
        'EGJB' => 336,
        'EGFE' => 157,
        'EGNR' => 45,
        'EGNJ' => 121,
        'EGPE' => 31,
        'EGPI' => 56,
        'EGNS' => 52,
        'EGJJ' => 277,
        'EGBP' => 436,
        'EGPA' => 58,
        'EGHC' => 398,
        'EGKH' => 70,
        'EGHF' => 32,
        'EGNM' => 681,
        'EGCM' => 29,
        'EGBG' => 469,
        'EGET' => 45,
        'EGGP' => 81,
        'EGLC' => 20,
        'EGKK' => 203,
        'EGLL' => 83,
        'EGGW' => 526,
        'EGSS' => 348,
        'EGAE' => 23,
        'EGMD' => 13,
        'EGCC' => 257,
        'EGCB' => 73,
        'EGNF' => 248,
        'EGNT' => 266,
        'EGHQ' => 390,
        'EGAD' => 9,
        'EGEN' => 56,
        'EGBK' => 424,
        'EGWU' => 124,
        'EGSH' => 117,
        'EGBN' => 138,
        'EGEO' => 34,
        'EGSV' => 194,
        'EGTH' => 120,
        'EGTK' => 270,
        'EGEP' => 92,
        'EGFP' => 15,
        'EGPT' => 397,
        'EGSF' => 26,
        'EGPK' => 65,
        'EGKR' => 222,
        'EGNE' => 87,
        'EGTO' => 426,
        'EGES' => 62,
        'EGCF' => 13,
        'EGPM' => 81,
        'EGHE' => 116,
        'EGCJ' => 26,
        'EGBS' => 317,
        'EGKA' => 7,
        'EGCV' => 275,
        'EGHI' => 44,
        'EGMC' => 55,
        'EGSY' => 164,
        'EGSG' => 185,
        'EGPO' => 26,
        'EGER' => 39,
        'EGPB' => 21,
        'EGNV' => 120,
        'EGFH' => 299,
        'EGBM' => 439,
        'EGHO' => 319,
        'EGPU' => 38,
        'EGNO' => 54,
        'EGBW' => 159,
        'EGCW' => 233,
        'EGFA' => 428,
        'EGEW' => 29,
        'EGLM' => 127,
        'EGPC' => 126,
        'EGNW' => 84,
        'EGBO' => 283,
        'EGTB' => 520,
        'EGHG' => 202,
        'EGQM' => 77,
        'EGOM' => 1066,
        'EGOV' => 37,
        'EGOY' => 56,
        'EGWC' => 272,
        'EGOS' => 249,
        'EGXE' => 132,
        'EGXC' => 25,
        'EGXG' => 29,
        'EGYD' => 218,
        'EGXV' => 36,
        'EGDR' => 218,
        'EGHT' => 20,
        'EGVO' => 405,
        'EGDY' => 75,
        'EGDX' => 163,
        'EGYM' => 75,
        'EGXH' => 174,
        'EGXH' => 174,
        'EGUL' => 32,
        'EGUN' => 33,
        'EGYC' => 65,
        'EGVN' => 288,
        'EGVN' => 288,
        'EGPS' => 150,
        'EGQK' => 22,
        'EGQL' => 38,
        'EGVA' => 286,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TO ADD
        //   0 => "EGSL"
        //  1 => "EGNL"
        //  2 => "EGBF"
        //  3 => "EGLK"
        //  4 => "EGEC"
        //  5 => "EGLJ"
        //  6 => "EGEL"
        //  7 => "EGEY"
        //  8 => "EGHA"
        //  9 => "EGPG"
        //  10 => "EGLD"
        //  11 => "EGBD"
        //  12 => "EGTU"
        //  13 => "EGSU"
        //  14 => "EGSR"
        //  15 => "EGED"
        //  16 => "EGTR"
        //  17 => "EGAB"
        //  18 => "EGEF"
        //  19 => "EGTF"
        //  20 => "EGCL"
        //  21 => "EGPI"
        //  22 => "EGKH"
        //  23 => "EGHF"
        //  24 => "EGCM"
        //  25 => "EGBG"
        //  26 => "EGET"
        //  27 => "EGCB"
        //  28 => "EGNF"
        //  29 => "EGAD"
        //  30 => "EGEN"
        //  31 => "EGBK"
        //  32 => "EGBN"
        //  33 => "EGSV"
        //  34 => "EGTH"
        //  35 => "EGEP"
        //  36 => "EGFP"
        //  37 => "EGPT"
        //  38 => "EGSF"
        //  39 => "EGKR"
        //  40 => "EGES"
        //  41 => "EGCF"
        //  42 => "EGCJ"
        //  43 => "EGBS"
        //  44 => "EGCV"
        //  45 => "EGSG"
        //  46 => "EGER"
        //  47 => "EGBM"
        //  48 => "EGHO"
        //  49 => "EGNO"
        //  50 => "EGBW"
        //  51 => "EGFA"
        //  52 => "EGEW"
        //  53 => "EGLM"
        //  54 => "EGNW"
        //  55 => "EGTB"
        //  56 => "EGHG"

        foreach (self::AIRFIELD_MAP as $airfield => $elevation) {
            DB::table('airfield')
                ->where('code', $airfield)
                ->update(['elevation' => $elevation, 'updated_at' => Carbon::now()]);
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
