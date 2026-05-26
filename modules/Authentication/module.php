<?php

declare(strict_types=1);

return [
    'name' => 'Authentication',
    'version' => '1.0.0',
    'enabled' => true,
    'dependencies' => [],
    'priority' => 10,
    'providers' => [
        Modules\Authentication\Providers\AuthenticationServiceProvider::class,
    ],
    'navigation' => [
        ['label' => 'Auth', 'path' => '/modules/authentication'],
    ],
    'permissions' => ['auth.manage'],
    'boot' => static function (): void {},
];
