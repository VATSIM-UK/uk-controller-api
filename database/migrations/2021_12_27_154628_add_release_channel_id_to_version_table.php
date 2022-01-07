<?php

use App\Models\Version\PluginReleaseChannel;
use App\Models\Version\Version;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReleaseChannelIdToVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('version', function (Blueprint $table) {
            $table->unsignedBigInteger('plugin_release_channel_id')
                ->after('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('version', function (Blueprint $table) {
            $table->dropColumn('plugin_release_channel_id');
        });
    }
}
