<?php

declare(strict_types=1);

use Core\Modules\ModuleManager;

$router->add('GET', '/api/v1/health', function () {
    header('Content-Type: application/json; charset=utf-8');
    return json_encode([
        'status' => 'ok',
        'framework' => 'NovaCore',
        'php' => PHP_VERSION,
        'timestamp' => gmdate(DATE_ATOM),
    ], JSON_UNESCAPED_SLASHES);
});

$router->add('GET', '/api/v1/modules', function () {
    /** @var ModuleManager $manager */
    $manager = $this->container->get(ModuleManager::class);
    header('Content-Type: application/json; charset=utf-8');
    return json_encode([
        'modules' => $manager->all(),
        'permissions' => $manager->permissions(),
    ], JSON_UNESCAPED_SLASHES);
});
