<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class RemoveDeletedSids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->sidsToDelete() as $sid) {
            try {
                Sid::where(
                    [
                        'airfield_id' => Airfield::where('code', $sid['airfield'])->firstOrFail()->id,
                        'identifier' => $sid['identifier'],
                    ]
                )->firstOrFail()->delete();
            } catch (Exception $exception) {
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to do here
    }

    private function sidsToDelete()
    {
        return [
            [
                'airfield' => 'EGBB',
                'identifier' => 'COWLY1L',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'COWLY3D',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'CPT1L',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'CPT4D',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'DTY1L',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'DTY4E',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'DTY5D',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'TNT1L',
            ],
            [
                'airfield' => 'EGBB',
                'identifier' => 'WCO1L',
            ],
            [
                'airfield' => 'EGCC',
                'identifier' => 'LISTO1S',
            ],
            [
                'airfield' => 'EGCC',
                'identifier' => 'LISTO1Z',
            ],
            [
                'airfield' => 'EGCC',
                'identifier' => 'LISTO1R',
            ],
            [
                'airfield' => 'EGCC',
                'identifier' => 'LISTO1Y',
            ],
            [
                'airfield' => 'EGJB',
                'identifier' => 'ORTAC2E',
            ],
            [
                'airfield' => 'EGJB',
                'identifier' => 'SKERY2W',
            ],
            [
                'airfield' => 'EGJB',
                'identifier' => 'SKERY2E',
            ],
            [
                'airfield' => 'EGKK',
                'identifier' => 'CLN2Z',
            ],
            [
                'airfield' => 'EGKK',
                'identifier' => 'CLN4X',
            ],
            [
                'airfield' => 'EGNT',
                'identifier' => 'GIRLI2X',
            ],
            [
                'airfield' => 'EGPK',
                'identifier' => 'OKNOB1K',
            ],
        ];
    }
}
