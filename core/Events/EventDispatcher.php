<?php

declare(strict_types=1);

namespace Core\Events;

final class EventDispatcher
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event] ??= [];
        $this->listeners[$event][] = $listener;
    }

    /** @param array<string,mixed> $payload */
    public function dispatch(string $event, array $payload = []): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener($payload);
        }
    }
}
