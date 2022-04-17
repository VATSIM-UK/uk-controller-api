<?php

namespace App\Services\IntentionCode\Builder;

use App\Exceptions\IntentionCode\IntentionCodeInvalidException;

class PriorityBuilder
{
    private int $priority = 0;

    public function withPriority(int $priority): void
    {
        if ($this->priority > 0) {
            throw new IntentionCodeInvalidException('Priority already set for intention code');
        }

        $this->checkPriority($priority, 'Intention code priority must be greater than 0');
        $this->priority = $priority;
    }

    public function get(): int
    {
        $this->checkPriority($this->priority, 'Intention code priority not set');
        return $this->priority;
    }

    private function checkPriority(int $priority, string $message): void
    {
        if ($priority < 1) {
            throw new IntentionCodeInvalidException($message);
        }
    }
}
