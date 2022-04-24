<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Database\Migrations\Migration;

class RecatEuCodeChanges extends Migration
{
    private const DESCRIPTION_CHANGES = [
        'A' => 'Super Heavy',
        'B' => 'Heavy',
        'C' => 'Upper',
        'D' => 'Medium',
        'E' => 'Small',
        'F' => 'Light',
    ];

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
        $this->updateDescriptions();
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

    private function updateDescriptions(): void
    {
        foreach (self::DESCRIPTION_CHANGES as $code => $description) {
            WakeCategory::where('wake_category_scheme_id', $this->recatSchemeId())
                ->where('code', $code)
                ->firstOrFail()
                ->update(['description' => $description]);
        }
    }

    private function swapCodes(array $codes): void
    {
        foreach ($codes as $previous => $new) {
            WakeCategory::where('wake_category_scheme_id', $this->recatSchemeId())
                ->where('code', $previous)
                ->firstOrFail()
                ->update(['code' => $new]);
        }
    }

    private function recatSchemeId(): int
    {
        return WakeCategoryScheme::where('key', 'RECAT_EU')->firstOrFail()->id;
    }
}
