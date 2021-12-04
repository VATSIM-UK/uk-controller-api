<?php

namespace App\Services\Metar;

use Illuminate\Support\Collection;

class DownloadedMetar
{
    private string $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function tokenise(): Collection
    {
        return collect($this->raw === '' ? [] : explode(' ', $this->raw));
    }
}
