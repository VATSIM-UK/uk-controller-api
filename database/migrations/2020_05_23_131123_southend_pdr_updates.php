<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SouthendPdrUpdates extends Migration
{
    public const PDRS = [
        'PDRDVR' => 'DVR PDR',
        'PDRCLN' => 'CLN PDR',
        'PDRBPK' => 'BPK PDR',
        'PDRCPT' => 'CPT PDR',
        'PDRLYD' => 'LYD PDR',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::PDRS as $old => $new) {
            DB::table('sid')->where('identifier', $old)->update(['identifier' => $new]);
        }
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::PDRS as $old => $new) {
            DB::table('sid')->where('identifier', $new)->update(['identifier' => $old]);
        }

        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }
}
