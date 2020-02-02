<?php

use App\Models\Hold\Hold;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Hold\HoldRestriction;

class CreateSectorFileIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sector_file_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('number')->comment('The github issue number');
            $table->boolean('plugin')->comment('Whether or not the issue has been recorded as a plugin issue');
            $table->boolean('api')->comment('Whether or not the issue has been recorded as a api issue');

            $table->unique('number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sector_file_issues');
    }
}
