<?php

declare(strict_types=1);

namespace Modules\Authentication\Providers;

use Core\Modules\Contracts\ModuleProviderInterface;
use Core\Modules\ModuleContext;

final class AuthenticationServiceProvider implements ModuleProviderInterface
{
    public function register(ModuleContext $context): void
    {
        $context->events()->listen('middleware.register', static function (): void {});
    }

    public function boot(ModuleContext $context): void
    {
        $context->events()->dispatch('module.auth.booted', ['module' => $context->manifest()['name']]);
    }
}
