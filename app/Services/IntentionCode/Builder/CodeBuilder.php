<?php

namespace App\Services\IntentionCode\Builder;

use App\Exceptions\IntentionCode\IntentionCodeInvalidException;
use Illuminate\Support\Str;

class CodeBuilder
{
    private array $code = [];

    public function singleCode(string $code): void
    {
        $this->checkOperation();
        if (Str::length($code) > 2 || Str::length($code) < 1) {
            throw new IntentionCodeInvalidException('Intention code must be one or two characters');
        }

        $this->checkOperation();
        $this->code = [
            'type' => 'single_code',
            'code' => $code
        ];
    }

    public function airfieldIdentifier(): void
    {
        $this->checkOperation();
        $this->code = [
            'type' => 'airfield_identifier',
        ];
    }

    public function get(): array
    {
        if (empty($this->code)) {
            throw new IntentionCodeInvalidException('No code set for this intention code');
        }

        return $this->code;
    }

    private function checkOperation(): void
    {
        if (!empty($this->code)) {
            throw new IntentionCodeInvalidException('Code is already set for intention code');
        }
    }
}
