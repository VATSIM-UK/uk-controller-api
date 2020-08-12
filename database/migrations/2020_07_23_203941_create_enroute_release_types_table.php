<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrouteReleaseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroute_release_types', function (Blueprint $table) {
            $table->id();
            $table->string('tag_string', 15)
                ->comment(
                    'What to display in the TAG for this kind of release. ' .
                        'Limited to 15 characters as max length of tag item.'
                );
            $table->string('description')->comment('Description of the release to be shown in the plugin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enroute_release_types');
    }
}
