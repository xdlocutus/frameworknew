<?php

declare(strict_types=1);

use Core\Modules\ModuleManager;
use Core\UI\View;

$moduleManager = $this->container->get(ModuleManager::class);
$navigation = $moduleManager->navigation();

$router->add('GET', '/', function () use ($moduleManager, $navigation) {
    $modules = $moduleManager->all();
    $permissions = $moduleManager->permissions();

    $moduleRows = '';
    foreach ($modules as $module) {
        $moduleRows .= sprintf('<li><strong>%s</strong> <span class="muted">(%s)</span></li>', htmlspecialchars((string) $module['name'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($module['path'], ENT_QUOTES, 'UTF-8'));
    }

    $permissionRows = '';
    foreach ($permissions as $permission) {
        $permissionRows .= sprintf('<li>%s</li>', htmlspecialchars($permission, ENT_QUOTES, 'UTF-8'));
    }

    $content = sprintf(
        '<h1>Framework Kernel Dashboard</h1><div class="card"><h3>System Health</h3><p class="muted">Status: operational</p><p class="muted">Runtime: PHP %s</p></div><div class="card"><h3>Installed Modules</h3><ul>%s</ul></div><div class="card"><h3>Registered Permissions</h3><ul>%s</ul></div><div class="card"><h3>Widget Zones</h3><p class="muted">No widgets registered. Modules can inject UI blocks at runtime.</p></div>',
        PHP_VERSION,
        $moduleRows ?: '<li class="muted">No modules installed.</li>',
        $permissionRows ?: '<li class="muted">No permissions registered.</li>'
    );

    return View::render('Dashboard', $content, $navigation);
});
