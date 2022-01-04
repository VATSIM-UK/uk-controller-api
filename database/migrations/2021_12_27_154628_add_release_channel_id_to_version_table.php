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


        $stableChannel = PluginReleaseChannel::where('name', 'stable')->firstOrFail();
        Version::all()->each(function (Version $version) use ($stableChannel) {
            $version->pluginReleaseChannel()->associate($stableChannel)->save();
        });

        Schema::table('version', function (Blueprint $table) {
            $table->foreign('plugin_release_channel_id')->references('id')->on('plugin_release_channels');
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
            $table->dropForeign('version_plugin_release_channel_id_foreign');
            $table->dropColumn('plugin_release_channel_id');
        });
    }
}
