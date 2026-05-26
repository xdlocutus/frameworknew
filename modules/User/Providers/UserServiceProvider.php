<?php

declare(strict_types=1);

namespace Modules\User\Providers;

use Core\Modules\Contracts\ModuleProviderInterface;
use Core\Modules\ModuleContext;

final class UserServiceProvider implements ModuleProviderInterface
{
    public function register(ModuleContext $context): void
    {
        $context->events()->listen('navigation.extend', static function (): void {});
    }

    public function boot(ModuleContext $context): void
    {
        $context->events()->dispatch('module.user.booted', ['module' => $context->manifest()['name']]);
    }
}
