<?php

declare(strict_types=1);

return [
    'name' => 'Settings',
    'version' => '1.0.1',
    'enabled' => true,
    'dependencies' => ['Authentication'],
    'priority' => 40,
    'providers' => [
        Modules\Settings\Providers\SettingsServiceProvider::class,
    ],
    'navigation' => [
        ['label' => 'Settings', 'path' => '/modules/settings'],
    ],
    'permissions' => ['settings.manage'],
    'boot' => static function (): void {},
];
