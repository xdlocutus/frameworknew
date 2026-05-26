<?php

declare(strict_types=1);

return [
    'name' => 'User',
    'version' => '1.1.0',
    'enabled' => true,
    'dependencies' => ['Authentication', 'RBAC'],
    'priority' => 30,
    'providers' => [
        Modules\User\Providers\UserServiceProvider::class,
    ],
    'navigation' => [
        ['label' => 'Users', 'path' => '/modules/user'],
    ],
    'permissions' => ['user.view', 'user.manage'],
    'boot' => static function (): void {},
];
