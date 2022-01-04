<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Version\PluginReleaseChannel;
use App\Models\Version\Version;

class AddReleaseChannelsToCurrent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stableChannel = PluginReleaseChannel::where('name', 'stable')->firstOrFail();
        Version::withTrashed()->get()->each(function (Version $version) use ($stableChannel) {
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
    }
}
