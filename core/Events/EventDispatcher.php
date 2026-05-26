<?php

declare(strict_types=1);

namespace Core\Events;

final class EventDispatcher
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    /** @var list<array{event:string,payload:array<string,mixed>,timestamp:float}> */
    private array $dispatchedEvents = [];

    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event] ??= [];
        $this->listeners[$event][] = $listener;
    }

    /** @param array<string,mixed> $payload */
    public function dispatch(string $event, array $payload = []): void
    {
        $this->dispatchedEvents[] = [
            'event' => $event,
            'payload' => $payload,
            'timestamp' => microtime(true),
        ];

        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener($payload);
        }
    }

    /** @return array<string,list<callable>> */
    public function listeners(): array
    {
        return $this->listeners;
    }

    /** @return list<array{event:string,payload:array<string,mixed>,timestamp:float}> */
    public function dispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}
