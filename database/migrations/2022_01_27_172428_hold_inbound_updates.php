<?php

use App\Models\Hold\Hold;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class HoldInboundUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->updateInboundHeading('VEXUB', 56);
        $this->updateInboundHeading('PEPIS', 3);
        $this->updateInboundHeading('SAM', 29);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function updateInboundHeading(string $identifier, int $heading)
    {
        Hold::whereHas('navaid', function (Builder $navaid) use ($identifier) {
            $navaid->where('identifier', $identifier);
        })->firstOrFail()
            ->update(['inbound_heading' => $heading]);
    }
}
