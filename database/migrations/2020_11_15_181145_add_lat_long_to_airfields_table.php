<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLatLongToAirfieldsTable extends Migration
{
    private const AIRFIELDS = [
        'EGAA' => [
            'latitude' => 54.6575012207,
            'longitude' => -6.2158298492399995,
        ],
        'EGAC' => [
            'latitude' => 54.618099212646484,
            'longitude' => -5.872499942779541,
        ],
        'EGAE' => [
            'latitude' => 55.04280090332031,
            'longitude' => -7.161109924316406,
        ],
        'EGBB' => [
            'latitude' => 52.453899383499994,
            'longitude' => -1.74802994728,
        ],
        'EGBE' => [
            'latitude' => 52.3697013855,
            'longitude' => -1.4797199964499999,
        ],
        'EGBJ' => [
            'latitude' => 51.89419937133789,
            'longitude' => -2.167220115661621,
        ],
        'EGBO' => [
            'latitude' => 52.51750183105469,
            'longitude' => -2.2594399452209473,
        ],
        'EGBP' => [
            'latitude' => 51.668095,
            'longitude' => -2.05694,
        ],
        'EGCC' => [
            'latitude' => 53.35369873046875,
            'longitude' => -2.2749500274658203,
        ],
        'EGCK' => [
            'latitude' => 53.101819,
            'longitude' => -4.337614,
        ],
        'EGCN' => [
            'latitude' => 53.4805378105,
            'longitude' => -1.01065635681,
        ],
        'EGDR' => [
            'latitude' => 50.08610153198242,
            'longitude' => -5.255710124969482,
        ],
        'EGDX' => [
            'latitude' => 51.4048,
            'longitude' => -3.43575,
        ],
        'EGDY' => [
            'latitude' => 51.0093994140625,
            'longitude' => -2.638819932937622,
        ],
        'EGEO' => [
            'latitude' => 56.4635009765625,
            'longitude' => -5.399670124053955,
        ],
        'EGFE' => [
            'latitude' => 51.833099365234375,
            'longitude' => -4.9611101150512695,
        ],
        'EGFF' => [
            'latitude' => 51.39670181274414,
            'longitude' => -3.343329906463623,
        ],
        'EGFH' => [
            'latitude' => 51.60530090332031,
            'longitude' => -4.0678300857543945,
        ],
        'EGGD' => [
            'latitude' => 51.382702,
            'longitude' => -2.71909,
        ],
        'EGGP' => [
            'latitude' => 53.33359909057617,
            'longitude' => -2.849720001220703,
        ],
        'EGGW' => [
            'latitude' => 51.874698638916016,
            'longitude' => -0.36833301186561584,
        ],
        'EGHC' => [
            'latitude' => 50.102798,
            'longitude' => -5.67056,
        ],
        'EGHE' => [
            'latitude' => 49.913299560546875,
            'longitude' => -6.291669845581055,
        ],
        'EGHH' => [
            'latitude' => 50.779998779296875,
            'longitude' => -1.8424999713897705,
        ],
        'EGHI' => [
            'latitude' => 50.95029830932617,
            'longitude' => -1.3567999601364136,
        ],
        'EGHQ' => [
            'latitude' => 50.44060134887695,
            'longitude' => -4.995409965515137,
        ],
        'EGHR' => [
            'latitude' => 50.85940170288086,
            'longitude' => -0.7591670155525208,
        ],
        'EGHT' => [
            'latitude' => 49.94559860229492,
            'longitude' => -6.331389904022217,
        ],
        'EGJA' => [
            'latitude' => 49.706104,
            'longitude' => -2.21472,
        ],
        'EGJB' => [
            'latitude' => 49.435001373291016,
            'longitude' => -2.6019699573516846,
        ],
        'EGJJ' => [
            'latitude' => 49.20790100097656,
            'longitude' => -2.195509910583496,
        ],
        'EGKA' => [
            'latitude' => 50.835602,
            'longitude' => -0.297222,
        ],
        'EGKB' => [
            'latitude' => 51.33079910279999,
            'longitude' => 0.0324999988079,
        ],
        'EGKK' => [
            'latitude' => 51.148102,
            'longitude' => -0.190278,
        ],
        'EGLC' => [
            'latitude' => 51.505299,
            'longitude' => 0.055278,
        ],
        'EGLF' => [
            'latitude' => 51.2757987976,
            'longitude' => -0.776332974434,
        ],
        'EGLL' => [
            'latitude' => 51.4706,
            'longitude' => -0.461941,
        ],
        'EGMC' => [
            'latitude' => 51.5713996887207,
            'longitude' => 0.6955559849739075,
        ],
        'EGMD' => [
            'latitude' => 50.95610046386719,
            'longitude' => 0.9391670227050781,
        ],
        'EGNC' => [
            'latitude' => 54.9375,
            'longitude' => -2.8091700077056885,
        ],
        'EGNE' => [
            'latitude' => 53.280601501464844,
            'longitude' => -0.9513890147209167,
        ],
        'EGNH' => [
            'latitude' => 53.77170181274414,
            'longitude' => -3.0286099910736084,
        ],
        'EGNJ' => [
            'latitude' => 53.57440185546875,
            'longitude' => -0.350832998752594,
        ],
        'EGNM' => [
            'latitude' => 53.86589813232422,
            'longitude' => -1.6605700254440308,
        ],
        'EGNR' => [
            'latitude' => 53.1781005859375,
            'longitude' => -2.9777801036834717,
        ],
        'EGNS' => [
            'latitude' => 54.08330154418945,
            'longitude' => -4.623889923095703,
        ],
        'EGNT' => [
            'latitude' => 55.037498474121094,
            'longitude' => -1.6916699409484863,
        ],
        'EGNV' => [
            'latitude' => 54.50920104980469,
            'longitude' => -1.4294099807739258,
        ],
        'EGNX' => [
            'latitude' => 52.8311004639,
            'longitude' => -1.32806003094,
        ],
        'EGOM' => [
            'latitude' => 55.0499992371,
            'longitude' => -2.54999995232,
        ],
        'EGOS' => [
            'latitude' => 52.79819869995117,
            'longitude' => -2.6680400371551514,
        ],
        'EGOV' => [
            'latitude' => 53.2481002808,
            'longitude' => -4.53533983231,
        ],
        'EGPA' => [
            'latitude' => 58.957801818847656,
            'longitude' => -2.9049999713897705,
        ],
        'EGPB' => [
            'latitude' => 59.87889862060547,
            'longitude' => -1.2955600023269653,
        ],
        'EGPC' => [
            'latitude' => 58.458900451660156,
            'longitude' => -3.09306001663208,
        ],
        'EGPD' => [
            'latitude' => 57.201900482177734,
            'longitude' => -2.197779893875122,
        ],
        'EGPE' => [
            'latitude' => 57.54249954223633,
            'longitude' => -4.047500133514404,
        ],
        'EGPF' => [
            'latitude' => 55.8718986511,
            'longitude' => -4.43306016922,
        ],
        'EGPH' => [
            'latitude' => 55.95000076293945,
            'longitude' => -3.372499942779541,
        ],
        'EGPK' => [
            'latitude' => 55.5093994140625,
            'longitude' => -4.586669921875,
        ],
        'EGPL' => [
            'latitude' => 57.48109817504883,
            'longitude' => -7.3627800941467285,
        ],
        'EGPM' => [
            'latitude' => 60.43280029296875,
            'longitude' => -1.2961100339889526,
        ],
        'EGPN' => [
            'latitude' => 56.45249938964844,
            'longitude' => -3.025830030441284,
        ],
        'EGPO' => [
            'latitude' => 58.215599060058594,
            'longitude' => -6.331110000610352,
        ],
        'EGPR' => [
            'latitude' => 57.0228,
            'longitude' => -7.44306,
        ],
        'EGPU' => [
            'latitude' => 56.49919891357422,
            'longitude' => -6.869170188903809,
        ],
        'EGQK' => [
            'latitude' => 57.6493988037,
            'longitude' => -3.56064009666,
        ],
        'EGQL' => [
            'latitude' => 56.37289810180664,
            'longitude' => -2.8684399127960205,
        ],
        'EGSC' => [
            'latitude' => 52.205002,
            'longitude' => 0.175,
        ],
        'EGSH' => [
            'latitude' => 52.6758003235,
            'longitude' => 1.28278005123,
        ],
        'EGSS' => [
            'latitude' => 51.8849983215,
            'longitude' => 0.234999999404,
        ],
        'EGSY' => [
            'latitude' => 53.394299,
            'longitude' => -1.38849,
        ],
        'EGTC' => [
            'latitude' => 52.0722007751,
            'longitude' => -0.616666972637,
        ],
        'EGTE' => [
            'latitude' => 50.73440170288086,
            'longitude' => -3.4138898849487305,
        ],
        'EGTK' => [
            'latitude' => 51.8368988037,
            'longitude' => -1.32000005245,
        ],
        'EGTO' => [
            'latitude' => 51.351898193359375,
            'longitude' => 0.5033329725265503,
        ],
        'EGUL' => [
            'latitude' => 52.409301757799994,
            'longitude' => 0.56099998951,
        ],
        'EGUN' => [
            'latitude' => 52.361900329589844,
            'longitude' => 0.48640599846839905,
        ],
        'EGVA' => [
            'latitude' => 51.6822013855,
            'longitude' => -1.7900300025900002,
        ],
        'EGVN' => [
            'latitude' => 51.75,
            'longitude' => -1.58362,
        ],
        'EGVO' => [
            'latitude' => 51.2341003418,
            'longitude' => -0.94282501936,
        ],
        'EGWC' => [
            'latitude' => 52.639999,
            'longitude' => -2.30558,
        ],
        'EGWU' => [
            'latitude' => 51.553001403799996,
            'longitude' => -0.418166995049,
        ],
        'EGXC' => [
            'latitude' => 53.0929985046,
            'longitude' => -0.166014000773,
        ],
        'EGXE' => [
            'latitude' => 54.2924,
            'longitude' => -1.5354,
        ],
        'EGXG' => [
            'latitude' => 53.834301,
            'longitude' => -1.1955,
        ],
        'EGXH' => [
            'latitude' => 52.34260177612305,
            'longitude' => 0.7729390263557434,
        ],
        'EGYD' => [
            'latitude' => 53.0303001404,
            'longitude' => -0.48324200511,
        ],
        'EGYM' => [
            'latitude' => 52.648395,
            'longitude' => 0.550692,
        ],
        'EGCW' => [
            'latitude' => 52.629444,
            'longitude' => -3.1525,
        ],
        'EGOY' => [
            'latitude' => 54.847222,
            'longitude' => -4.948333,
        ],
        'EGPS' => [
            'latitude' => 57.517833,
            'longitude' => -1.861333,
        ],
        'EGQM' => [
            'latitude' => 55.421944,
            'longitude' => -1.603333,
        ],
        'EGXV' => [
            'latitude' => 53.876944,
            'longitude' => -0.4375,
        ],
        'EGYC' => [
            'latitude' => 52.754722,
            'longitude' => 1.357222,
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'airfield',
            function (Blueprint $table) {
                $table->double('latitude', 10, 8)
                    ->after('code')
                    ->comment('The latitude of the airfield in decimal degrees');
                $table->double('longitude', 11, 8)
                    ->after('latitude')
                    ->comment('The longitude of the airfield in decimal degrees');
            }
        );

        foreach (self::AIRFIELDS as $icao => $data) {
            DB::table('airfield')
                ->where('code', $icao)
                ->update(
                    [
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                    ]
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'airfield',
            function (Blueprint $table) {
                $table->dropColumn('latitude');
                $table->dropColumn('longitude');
            }
        );
    }
}
