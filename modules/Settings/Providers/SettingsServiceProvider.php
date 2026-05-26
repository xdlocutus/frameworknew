<?php

declare(strict_types=1);

namespace Modules\Settings\Providers;

use Core\Modules\Contracts\ModuleProviderInterface;
use Core\Modules\ModuleContext;

final class SettingsServiceProvider implements ModuleProviderInterface
{
    public function register(ModuleContext $context): void
    {
        $context->events()->listen('widgets.register', static function (): void {});
    }

    public function boot(ModuleContext $context): void
    {
        $context->events()->dispatch('module.settings.booted', ['module' => $context->manifest()['name']]);
    }
}
