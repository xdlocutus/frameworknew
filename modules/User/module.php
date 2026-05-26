<?php

declare(strict_types=1);

return [
    'name' => 'User',
    'enabled' => true,
    'navigation' => [
        ['label' => 'Users', 'path' => '/modules/user'],
    ],
    'permissions' => ['user.view', 'user.manage'],
    'boot' => static function (): void {},
];
