<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        AltimeterSettingRegion::whereIn('key', ['ASR_LONDON', 'ASR_MANCHESTER', 'ASR_SCOTTISH'])
            ->update(['adjustment' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        AltimeterSettingRegion::whereIn('key', ['ASR_LONDON', 'ASR_MANCHESTER', 'ASR_SCOTTISH'])
            ->update(['adjustment' => -1]);
    }
};
