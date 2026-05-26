<?php

declare(strict_types=1);

return [
    'name' => 'Dashboard',
    'version' => '1.0.0',
    'enabled' => true,
    'dependencies' => ['Authentication'],
    'priority' => 20,
    'providers' => [],
    'navigation' => [
        ['label' => 'Dashboard', 'path' => '/modules/dashboard'],
    ],
    'permissions' => ['dashboard.view'],
    'boot' => static function (): void {},
];
