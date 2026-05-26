<?php

declare(strict_types=1);

return [
    'name' => 'RBAC',
    'version' => '1.2.0',
    'enabled' => true,
    'dependencies' => ['Authentication'],
    'priority' => 20,
    'providers' => [
        Modules\RBAC\Providers\RBACServiceProvider::class,
    ],
    'navigation' => [
        ['label' => 'RBAC', 'path' => '/modules/rbac'],
    ],
    'permissions' => ['rbac.roles.manage', 'rbac.permissions.manage'],
    'boot' => static function (): void {},
];
