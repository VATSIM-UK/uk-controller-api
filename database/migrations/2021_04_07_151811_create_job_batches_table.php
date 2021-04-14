<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateJobBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'CREATE TABLE `job_batches` (
                `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `total_jobs` int NOT NULL,
                `pending_jobs` int NOT NULL,
                `failed_jobs` int NOT NULL,
                `failed_job_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `options` mediumtext COLLATE utf8mb4_unicode_ci,
                `cancelled_at` int DEFAULT NULL,
                `created_at` int NOT NULL,
                `finished_at` int DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_batches');
    }
}
