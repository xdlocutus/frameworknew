<?php

declare(strict_types=1);

namespace Core\Modules\Contracts;

use Core\Modules\ModuleContext;

interface ModuleProviderInterface
{
    public function register(ModuleContext $context): void;

    public function boot(ModuleContext $context): void;
}
