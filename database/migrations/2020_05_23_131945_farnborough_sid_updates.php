<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FarnboroughSidUpdates extends Migration
{
    const SIDS = [
        'GWC1F' => 'GWC2F',
        'GWC1L' => 'GWC2L',
        'HAZEL1F' => 'HAZEL2F',
        'HAZEL1L' => 'HAZEL2L',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::SIDS as $old => $new) {
            DB::table('sid')->where('identifier', $old)->update(['identifier' => $new]);
        }
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFFS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::SIDS as $old => $new) {
            DB::table('sid')->where('identifier', $new)->update(['identifier' => $old]);
        }

        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFFS');
    }
}
