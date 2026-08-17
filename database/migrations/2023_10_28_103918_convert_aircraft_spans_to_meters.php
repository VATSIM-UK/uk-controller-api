<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->decimal('wingspan', 5, 2)->comment('Wingpsan in meters')->change();
            $table->decimal('length', 5, 2)->comment('Length in meters')->change();
        });
    }
};
