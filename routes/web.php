<?php

declare(strict_types=1);

use Core\Modules\ModuleManager;
use Core\UI\Components;
use Core\UI\View;

$moduleManager = $this->container->get(ModuleManager::class);
$navigation = $moduleManager->navigation();

$router->add('GET', '/', function () use ($moduleManager, $navigation) {
    $modules = $moduleManager->all();
    $permissions = $moduleManager->permissions();
    $meta = $moduleManager->frameworkMeta();
    $compatIssues = $moduleManager->compatibilityIssues();

    $moduleRows = [];
    foreach ($modules as $module) {
        $moduleRows[] = sprintf('<tr><td class="px-4 py-3 text-sm font-semibold text-slate-900">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td><td class="px-4 py-3 text-sm text-slate-500">%d</td></tr>', htmlspecialchars((string) $module['name'], ENT_QUOTES, 'UTF-8'), htmlspecialchars((string) $module['version'], ENT_QUOTES, 'UTF-8'), htmlspecialchars((string) $module['path'], ENT_QUOTES, 'UTF-8'), (int) ($module['priority'] ?? 100));
    }

    $permissionRows = [];
    foreach ($permissions as $permission) {
        $permissionRows[] = sprintf('<tr><td class="px-4 py-3 text-sm text-slate-700">%s</td></tr>', htmlspecialchars($permission, ENT_QUOTES, 'UTF-8'));
    }

    $compatibilityBody = $compatIssues === []
        ? '<p class="text-sm text-slate-500">All enabled modules passed dependency checks and lifecycle validation.</p>'
        : '<ul class="text-sm text-slate-500"><li>' . implode('</li><li>', array_map(static fn (array $issue): string => htmlspecialchars($issue['module'] . ' requires ' . $issue['depends_on'], ENT_QUOTES, 'UTF-8'), $compatIssues)) . '</li></ul>';

    $content = '<div class="stack">'
        . '<header><h1 class="text-slate-900" style="margin:0;font-size:1.75rem;">Framework Control Plane</h1><p class="text-sm text-slate-500" style="margin-top:8px;">A modular runtime designed for provider-driven extension and isolated package-style modules.</p></header>'
        . '<section class="metrics"><article class="metric"><p class="metric-label">Framework</p><p class="metric-value">' . htmlspecialchars($meta['name'], ENT_QUOTES, 'UTF-8') . '</p></article><article class="metric"><p class="metric-label">Version</p><p class="metric-value">' . htmlspecialchars($meta['version'], ENT_QUOTES, 'UTF-8') . '</p></article><article class="metric"><p class="metric-label">Installed Modules</p><p class="metric-value">' . (int) $meta['modules'] . '</p></article><article class="metric"><p class="metric-label">Runtime</p><p class="metric-value">PHP ' . htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') . '</p></article></section>'
        . Components::card('Installed Module Registry', Components::table(['Module', 'Version', 'Path', 'Boot Priority'], $moduleRows), 'Modules are loaded through manifests, sorted by priority, and booted via provider lifecycle hooks.')
        . Components::card('Permission Surface', Components::table(['Permission Key'], $permissionRows), 'Permissions are aggregated from enabled module manifests to keep security declarations module-local.')
        . Components::card('Compatibility & Lifecycle', $compatibilityBody, 'Dependency checks run before boot to ensure modules remain isolated and composable.')
        . '</div>';

    return View::render('Dashboard', $content, $navigation);
});
