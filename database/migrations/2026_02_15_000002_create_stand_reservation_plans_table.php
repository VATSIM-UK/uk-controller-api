<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stand_reservation_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_email');
            $table->json('payload');
            $table->timestamp('approval_due_at');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->unsignedInteger('submitted_by')->nullable();
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('imported_reservations')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('user')->nullOnDelete();
            $table->foreign('submitted_by')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stand_reservation_plans');
    }
};
