<?php

declare(strict_types=1);

return [
    'name' => 'RBAC',
    'enabled' => true,
    'navigation' => [
        ['label' => 'RBAC', 'path' => '/modules/rbac'],
    ],
    'permissions' => ['rbac.roles.manage', 'rbac.permissions.manage'],
    'boot' => static function (): void {},
];
