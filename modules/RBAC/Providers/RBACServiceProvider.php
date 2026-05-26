<?php

declare(strict_types=1);

namespace Modules\RBAC\Providers;

use Core\Modules\Contracts\ModuleProviderInterface;
use Core\Modules\ModuleContext;

final class RBACServiceProvider implements ModuleProviderInterface
{
    public function register(ModuleContext $context): void
    {
        $context->events()->listen('permissions.extend', static function (): void {});
    }

    public function boot(ModuleContext $context): void
    {
        $context->events()->dispatch('module.rbac.booted', ['module' => $context->manifest()['name']]);
    }
}
