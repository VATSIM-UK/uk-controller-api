<?php

namespace App\Events\Database;

use Illuminate\Support\Collection;

class DatabaseTablesUpdated
{
    private Collection $tables;

    public function __construct(Collection $tables)
    {
        $this->tables = $tables;
    }

    public function getTables(): Collection
    {
        return $this->tables;
    }
}
