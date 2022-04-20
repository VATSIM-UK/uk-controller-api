<?php

use App\Models\Dependency\Dependency;
use App\Services\IntentionCode\IntentionCodeService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixIntentionCodeDependencyPath extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Dependency::where('key', 'DEPENDENCY_INTENTION_CODES')
            ->update(['action' => sprintf('%s@getIntentionCodesDependency', IntentionCodeService::class)]);
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
}
