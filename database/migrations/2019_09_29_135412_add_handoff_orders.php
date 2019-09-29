<?php

use App\Models\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddHandoffOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getHandoffData() as $data) {
            $order = 1;
            $positions = [];

            // Get all the positions
            foreach ($data['positions'] as $position) {
                $positions[] = [
                    'controller_position_id' => ControllerPosition::where('callsign', $position)->firstOrFail()->id,
                    'order' => $order++,
                ];
            }

            // Add data
            Sid::where(
                [
                    'identifier' => $data['identifier'],
                    'airfield_id' => $data['airfield_id'],
                ]
            )->firstOrFail()
                ->handoffs()
                ->attach($positions);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sids = Sid::all();
        $sids->each(function (Sid $sid) {
            $sid->handoffs()->detach($sid->handoffs->pluck('id')->toArray());
        });
    }

    private function getHandoffData(): array
    {
        return [

            // EGBB
            [
                'identifier' => 'ADMEX1D',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'ADMEX1M',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'COWLY2Y',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'CPT2Y',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'DTY2Y',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'DTY4F',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'LUVUM1M',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'LUVUM1L',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'TNT1K',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'TNT4G',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'TNT4D',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'TNT6E',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'UMLUX1M',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'UNGAP1D',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'UNGAP1M',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'WCO2Y',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'WCO5D',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'WHI1L',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
            [
                'identifier' => 'WHI4D',
                'airfield_id' => Airfield::where('code', 'EGBB')->firstOrFail()->id,
                'positions' => [
                    'EGBB_APP',
                    'LON_C_CTR',
                    'LON_SC_CTR',
                    'LON_CTR',
                ],
            ],
        ];
    }
}
