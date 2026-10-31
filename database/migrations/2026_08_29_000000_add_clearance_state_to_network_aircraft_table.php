<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('network_aircraft', function (Blueprint $table): void {
            $table->boolean('clearance_flag')->default(false)->after('remarks');
            $table->string('ground_state', 10)->nullable()->after('clearance_flag');

            // As reported by the plugin, which batches - not when we received it.
            $table->timestamp('clearance_flag_updated_at')->nullable()->after('ground_state');
            $table->timestamp('ground_state_updated_at')->nullable()->after('clearance_flag_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('network_aircraft', function (Blueprint $table): void {
            $table->dropColumn([
                'clearance_flag',
                'ground_state',
                'clearance_flag_updated_at',
                'ground_state_updated_at',
            ]);
        });
    }
};
