<?php

namespace App\Services;

use App\Events\Database\DatabaseTablesUpdated;
use App\Models\Database\DatabaseTable;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseService
{
    private InformationSchemaService $informationSchemaService;

    public function __construct(InformationSchemaService $informationSchemaService)
    {
        $this->informationSchemaService = $informationSchemaService;
    }

    /**
     * Compare the stored information about database tables to the information_schema
     * to see if something has been updated.
     */
    public function updateTableStatus(): void
    {
        DB::transaction(function () {
            $cachedTableStatistics = $this->getCachedTableStatistics();
            $liveTableStatistics = $this->getLiveTableStatistics($cachedTableStatistics->pluck('name'));
            $updatedTables = $this->getUpdatedTables($cachedTableStatistics, $liveTableStatistics);
            if ($updatedTables->isEmpty()) {
                return;
            }

            $this->setTableUpdateTimes($updatedTables, $liveTableStatistics);
            event(new DatabaseTablesUpdated($updatedTables));
        });
    }

    private function getUpdatedTables(Collection $cachedTableStatistics, Collection $liveTableStatistics): Collection
    {
        return $cachedTableStatistics->filter(function (DatabaseTable $table) use ($liveTableStatistics) {
            return $table->updated_at === null ||
                (
                    $liveTableStatistics->get($table->name) !== null &&
                    $table->updated_at < $liveTableStatistics->get($table->name)
                );
        });
    }

    private function setTableUpdateTimes(Collection $updatedTables, Collection $liveTableStatistics): void
    {
        $updatedTables->each(function (DatabaseTable $table) use ($liveTableStatistics) {
            $table->updated_at = $liveTableStatistics->get($table->name) ?: Carbon::now();
            $table->save();
        });
    }

    /**
     * Use the information_schema to work at the last time at which the
     * required tables were updated.
     */
    public function getLiveTableStatistics(Collection $tables): Collection
    {
        DB::connection('mysql_analyze')->statement('ANALYZE TABLE ' . $tables->implode(','));
        return $this->informationSchemaService->getInformationSchemaTables($tables->toArray())
            ->mapWithKeys(function (object $table) {
                return [
                    $table->TABLE_NAME => $table->UPDATE_TIME ? Carbon::parse($table->UPDATE_TIME) : null,
                ];
            });
    }

    private function getCachedTableStatistics(): Collection
    {
        return DatabaseTable::all();
    }
}
