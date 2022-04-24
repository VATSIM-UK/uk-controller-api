<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecatEuCodeChanges extends Migration
{
    private const CODE_CHANGES = [
        'A' => 'J',
        'B' => 'H',
        'C' => 'U',
        'D' => 'M',
        'E' => 'S',
        'F' => 'L',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->swapCodes(self::CODE_CHANGES);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->swapCodes(array_flip(self::CODE_CHANGES));
    }

    private function swapCodes(array $codes): void
    {
        $recatSchemeId = WakeCategoryScheme::where('key', 'RECAT_EU')->firstOrFail()->id;
        foreach ($codes as $previous => $new) {
            WakeCategory::where('wake_category_scheme_id', $recatSchemeId)
                ->where('code', $previous)
                ->firstOrFail()
                ->update(['code' => $new]);
        }
    }
}
