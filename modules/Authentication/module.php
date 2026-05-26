<?php

declare(strict_types=1);

return [
    'name' => 'Authentication',
    'enabled' => true,
    'navigation' => [
        ['label' => 'Auth', 'path' => '/modules/authentication'],
    ],
    'permissions' => ['auth.manage'],
    'boot' => static function (): void {},
];
