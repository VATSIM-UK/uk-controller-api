<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
class InformationSchemaService
{
    public function getInformationSchemaTables(array $tables): Collection
    {
        return DB::connection('information_schema')->table('TABLES')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->whereIn('TABLE_NAME', $tables)
            ->get();
    }
}
