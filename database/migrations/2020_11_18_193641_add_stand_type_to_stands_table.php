<?php

use App\Models\Stand\StandType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStandTypeToStandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')
                ->after('longitude')
                ->nullable()
                ->default(StandType::where('key', 'PASSENGER')->first()->id)
                ->comment('The type of stand if relevant - international, domestic, cargo etc');
            $table->foreign('type_id')->references('id')->on('stand_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropForeign('stands_type_id_foreign');
            $table->dropColumn('type_id');
        });
    }
}
