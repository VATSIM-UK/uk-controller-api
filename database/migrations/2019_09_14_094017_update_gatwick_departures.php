<?php

use App\Models\Airfield\Airfield;
use App\Models\Sid;
use Illuminate\Database\Migrations\Migration;

class UpdateGatwickDepartures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatwick = Airfield::where('code', 'EGKK')->firstOrFail()->id;

        $sids = [
            'LAM5M' => 'LAM6M',
            'LAM5V' => 'LAM6V',
            'CLN9M' => 'CLN1M',
            'CLN9V' => 'CLN1V',
            'DVR9M' => 'DVR1M',
            'DVR9V' => 'DVR1V',
        ];

        foreach ($sids as $old => $new) {
            $this->updateSid($gatwick, $old, $new);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $gatwick = Airfield::where('code', 'EGKK')->firstOrFail()->id;

        $sids = [
            'LAM6M' => 'LAM5M',
            'LAM6V' => 'LAM5V',
            'CLN1M' => 'CLN9M',
            'CLN1V' => 'CLN9V',
            'DVR1M' => 'DVR9M',
            'DVR1V' => 'DVR9V',
        ];

        foreach ($sids as $old => $new) {
            $this->updateSid($gatwick, $old, $new);
        }
    }

    private function updateSid(int $gatwickAirfieldId, string $oldIdentifier, string $newIdentifier) : void
    {
        Sid::where('airfield_id', $gatwickAirfieldId)
            ->where('identifier', $oldIdentifier)
            ->update(['identifier' => $newIdentifier]);
    }
}
