<?php

declare(strict_types=1);

namespace Core\Modules;

use Core\Container\Container;
use Core\Events\EventDispatcher;

final class ModuleContext
{
    /** @param array<string,mixed> $manifest */
    public function __construct(
        private readonly Container $container,
        private readonly EventDispatcher $events,
        private readonly array $manifest,
    ) {}

    public function container(): Container
    {
        return $this->container;
    }

    public function events(): EventDispatcher
    {
        return $this->events;
    }

    /** @return array<string,mixed> */
    public function manifest(): array
    {
        return $this->manifest;
    }
}
