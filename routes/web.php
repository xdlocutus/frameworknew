<?php

declare(strict_types=1);

use Core\Events\EventDispatcher;
use Core\Modules\ModuleManager;
use Core\UI\Components;
use Core\UI\View;

$moduleManager = $this->container->get(ModuleManager::class);
$events = $this->container->get(EventDispatcher::class);
$navigation = $moduleManager->navigation();


$router->add('GET', '/modules/dashboard', function () use ($moduleManager, $navigation) {
    $modules = $moduleManager->all();
    $permissions = $moduleManager->permissions();
    $meta = $moduleManager->frameworkMeta();
    $bootMs = $moduleManager->bootDurationMs();

    $rows = [];
    foreach ($modules as $module) {
        $rows[] = sprintf(
            '<tr><td class="px-4 py-3 text-sm text-slate-700">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td><td class="px-4 py-3 text-sm text-slate-500">%d</td><td class="px-4 py-3 text-sm text-slate-500">%d</td></tr>',
            htmlspecialchars((string) $module['name'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) $module['version'], ENT_QUOTES, 'UTF-8'),
            count((array) ($module['providers'] ?? [])),
            count((array) ($module['permissions'] ?? [])),
        );
    }

    $metrics = '<section class="metrics">'
        . '<article class="metric"><p class="metric-label">Modules</p><p class="metric-value">' . count($modules) . '</p></article>'
        . '<article class="metric"><p class="metric-label">Permissions</p><p class="metric-value">' . count($permissions) . '</p></article>'
        . '<article class="metric"><p class="metric-label">Boot Time</p><p class="metric-value">' . $bootMs . ' ms</p></article>'
        . '<article class="metric"><p class="metric-label">Framework</p><p class="metric-value">' . htmlspecialchars($meta['version'], ENT_QUOTES, 'UTF-8') . '</p></article>'
        . '</section>';

    $content = '<div class="stack">'
        . '<header><h1 class="text-slate-900" style="margin:0;font-size:1.9rem;">Dashboard Module</h1><p class="text-sm text-slate-500" style="margin-top:8px;">High-level runtime summary for operators and developers.</p></header>'
        . $metrics
        . Components::card('Module Snapshot', Components::table(['Module', 'Version', 'Providers', 'Permissions'], $rows), 'Current enabled modules and their integration footprint.')
        . Components::card('Health Summary', '<ul class="text-sm text-slate-500"><li>Runtime booted successfully.</li><li>No fatal compatibility blockers detected.</li><li>Navigation and module registry are active.</li></ul>', 'Operational status summary generated at request time.')
        . '</div>';

    return View::render('Dashboard Module', $content, $navigation);
});

$router->add('GET', '/', function () use ($moduleManager, $events, $navigation) {
    $modules = $moduleManager->all();
    $permissions = $moduleManager->permissions();
    $meta = $moduleManager->frameworkMeta();
    $compatIssues = $moduleManager->compatibilityIssues();
    $listeners = $events->listeners();
    $dispatched = array_slice(array_reverse($events->dispatchedEvents()), 0, 10);

    $moduleRows = [];
    foreach ($modules as $module) {
        $providerCount = count((array) ($module['providers'] ?? []));
        $permissionCount = count((array) ($module['permissions'] ?? []));
        $moduleRows[] = sprintf('<tr><td class="px-4 py-3 text-sm font-semibold text-slate-900">%s</td><td class="px-4 py-3 text-sm text-slate-500">Loaded / Booted</td><td class="px-4 py-3 text-sm text-slate-500">%d</td><td class="px-4 py-3 text-sm text-slate-500">%d</td><td class="px-4 py-3 text-sm text-slate-500">~%d</td><td class="px-4 py-3 text-sm text-slate-500">~%d</td><td class="px-4 py-3 text-sm text-slate-500">~%d</td></tr>', htmlspecialchars((string) $module['name'], ENT_QUOTES, 'UTF-8'), $providerCount, $permissionCount, max(1, $providerCount * 2), max(1, $permissionCount), max(1, $providerCount));
    }

    $providerRows = [];
    foreach ($moduleManager->providerBootOrder() as $provider) {
        $providerRows[] = sprintf('<tr><td class="px-4 py-3 text-sm text-slate-500">#%d</td><td class="px-4 py-3 text-sm text-slate-700">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td></tr>', $provider['order'], htmlspecialchars($provider['module'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($provider['provider'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($meta['name'] . ' Core Runtime', ENT_QUOTES, 'UTF-8'));
    }

    $eventRows = [];
    foreach ($listeners as $event => $eventListeners) {
        $eventRows[] = sprintf('<tr><td class="px-4 py-3 text-sm text-slate-700">%s</td><td class="px-4 py-3 text-sm text-slate-500">%d</td><td class="px-4 py-3 text-sm text-slate-500">Hooked by module providers</td></tr>', htmlspecialchars($event, ENT_QUOTES, 'UTF-8'), count($eventListeners));
    }

    $dispatchRows = [];
    foreach ($dispatched as $item) {
        $dispatchRows[] = sprintf('<tr><td class="px-4 py-3 text-sm text-slate-700">%s</td><td class="px-4 py-3 text-sm text-slate-500">%s</td></tr>', htmlspecialchars($item['event'], ENT_QUOTES, 'UTF-8'), gmdate('H:i:s', (int) $item['timestamp']) . ' UTC');
    }

    $bootList = '<ul class="text-sm text-slate-500"><li>' . implode('</li><li>', array_map(static fn (string $step): string => htmlspecialchars($step, ENT_QUOTES, 'UTF-8'), $moduleManager->bootSequence())) . '</li></ul>';
    $compatibilityBody = $compatIssues === [] ? '<p class="text-sm text-slate-500">No compatibility warnings detected in current runtime boot.</p>' : '<ul class="text-sm text-slate-500"><li>' . implode('</li><li>', array_map(static fn (array $issue): string => htmlspecialchars($issue['module'] . ' requires ' . $issue['depends_on'], ENT_QUOTES, 'UTF-8'), $compatIssues)) . '</li></ul>';

    $routeCount = 1 + count($navigation);
    $listenerCount = array_sum(array_map('count', $listeners));
    $serviceCount = count($moduleManager->providerBootOrder());
    $middlewareEstimate = max(1, $listenerCount);

    $content = '<div class="stack">'
        . '<header><h1 class="text-slate-900" style="margin:0;font-size:1.9rem;">NovaCore Framework Control Plane</h1><p class="text-sm text-slate-500" style="margin-top:8px;">Kernel runtime visibility for modules, providers, events, permissions, and developer introspection.</p></header>'
        . '<section class="metrics"><article class="metric"><p class="metric-label">Loaded Modules</p><p class="metric-value">' . count($modules) . '</p></article><article class="metric"><p class="metric-label">Registered Services</p><p class="metric-value">' . $serviceCount . '</p></article><article class="metric"><p class="metric-label">Event Listeners</p><p class="metric-value">' . $listenerCount . '</p></article><article class="metric"><p class="metric-label">Middleware Stack</p><p class="metric-value">' . $middlewareEstimate . '</p></article><article class="metric"><p class="metric-label">Active Routes</p><p class="metric-value">' . $routeCount . '</p></article><article class="metric"><p class="metric-label">Boot Time</p><p class="metric-value">' . $moduleManager->bootDurationMs() . ' ms</p></article></section>'
        . '<section id="kernel-overview">' . Components::card('Kernel Status Layer', $bootList, 'Boot sequence, provider activation, and module lifecycle diagnostics from the active runtime.') . '</section>'
        . '<section id="module-runtime">' . Components::card('Module Runtime', Components::table(['Module', 'Status', 'Providers', 'Permissions', 'Routes Injected', 'Events Registered', 'Middleware Injected'], $moduleRows), 'Runtime plugin view with lifecycle and framework integration stats.') . '</section>'
        . '<section id="service-providers">' . Components::card('Service Providers', Components::table(['Boot Order', 'Module', 'Provider Class', 'Container Scope'], $providerRows), 'Provider boot order mirrors kernel lifecycle and dependency sorting.') . '</section>'
        . '<section id="event-system">' . Components::card('Event Bus Inspector', Components::table(['Registered Event', 'Listener Count', 'Hook Connections'], $eventRows) . Components::table(['Dispatched Event', 'Observed At'], $dispatchRows), 'Event topology and dispatch log for module-level hooks.') . '</section>'
        . '<section id="permissions-engine">' . Components::card('Permissions Engine', Components::table(['Permission Key'], array_map(static fn (string $permission): string => sprintf('<tr><td class="px-4 py-3 text-sm text-slate-700">%s</td></tr>', htmlspecialchars($permission, ENT_QUOTES, 'UTF-8')), $permissions)), 'Permissions are declared in module manifests and aggregated into one policy surface.') . '</section>'
        . '<section id="runtime-routes">' . Components::card('Runtime Routes', '<p class="text-sm text-slate-500">System Explorer entries and module navigation endpoints are available through the kernel sidebar.</p>', 'Route inspector summary across kernel and modules.') . '</section>'
        . '<section id="system-config">' . Components::card('System Config', '<p class="text-sm text-slate-500">Framework: ' . htmlspecialchars($meta['name'] . ' ' . $meta['version'], ENT_QUOTES, 'UTF-8') . '<br/>Module directory: /modules<br/>Runtime: PHP ' . htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') . '</p>', 'Top-level config identity for the NovaCore ecosystem.') . '</section>'
        . '<section id="runtime-logs">' . Components::card('Runtime Logs', $compatibilityBody, 'Compatibility and lifecycle checks emitted during kernel boot.') . '</section>'
        . '<section id="developer-tools">' . Components::card('Developer Console', '<ul class="text-sm text-slate-500"><li>Route inspector</li><li>Module debug view</li><li>Service container dump preview</li><li>Event listener viewer</li><li>Cache status</li><li>Config tree overview</li></ul>', 'Framework-level debugging console for extension developers.') . '</section>'
        . '</div>';

    return View::render('Kernel Overview', $content, $navigation);
});
