<?php

use App\Models\Hold\Hold;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class CardiffHoldInboundCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Hold::whereHas('navaid', function (Builder $navaid) {
            $navaid->where('identifier', 'CDF');
        })->update(['inbound_heading' => 297]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Hold::whereHas('navaid', function (Builder $navaid) {
            $navaid->where('identifier', 'CDF');
        })->update(['inbound_heading' => 298]);
    }
}
