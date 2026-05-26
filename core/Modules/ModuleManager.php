<?php

declare(strict_types=1);

namespace Core\Modules;

use Core\Container\Container;
use Core\Events\EventDispatcher;
use Core\Modules\Contracts\ModuleProviderInterface;

final class ModuleManager
{
    /** @var array<int, array<string, mixed>> */
    private array $modules = [];

    /** @var array<int, array{module:string, depends_on:string}> */
    private array $compatibilityIssues = [];

    public function __construct(
        private readonly string $path,
        private readonly Container $container,
        private readonly EventDispatcher $events,
        private readonly string $frameworkVersion = '1.0.0',
    ) {}

    public function boot(): void
    {
        foreach (glob($this->path . '/*/module.php') ?: [] as $file) {
            $manifest = require $file;
            if (!is_array($manifest)) {
                continue;
            }

            $module = $this->normalizeManifest($manifest, dirname($file));
            if (($module['enabled'] ?? false) !== true) {
                continue;
            }

            if (!$this->isCompatible($module)) {
                continue;
            }

            $this->modules[] = $module;
        }

        usort($this->modules, static fn (array $a, array $b): int => (int) ($a['priority'] ?? 100) <=> (int) ($b['priority'] ?? 100));

        foreach ($this->modules as $module) {
            $this->registerProviders($module);
        }

        foreach ($this->modules as $module) {
            $boot = $module['boot'] ?? null;
            if (is_callable($boot)) {
                $boot($this->container);
            }

            $this->bootProviders($module);

            $this->events->dispatch('module.booted', ['module' => $module['name']]);
        }
    }

    /** @return array<int, array<string,mixed>> */
    public function all(): array
    {
        return $this->modules;
    }

    /** @return array<int, array{label:string,path:string}> */
    public function navigation(): array
    {
        $navigation = [];
        foreach ($this->modules as $module) {
            if (!isset($module['navigation']) || !is_array($module['navigation'])) {
                continue;
            }
            foreach ($module['navigation'] as $item) {
                if (is_array($item) && isset($item['label'], $item['path'])) {
                    $navigation[] = ['label' => (string) $item['label'], 'path' => (string) $item['path']];
                }
            }
        }

        return $navigation;
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        $permissions = [];
        foreach ($this->modules as $module) {
            foreach (($module['permissions'] ?? []) as $permission) {
                if (is_string($permission)) {
                    $permissions[] = $permission;
                }
            }
        }

        return array_values(array_unique($permissions));
    }

    /** @return array<int, array{module:string,depends_on:string}> */
    public function compatibilityIssues(): array
    {
        return $this->compatibilityIssues;
    }

    /** @return array{name:string,version:string,modules:int} */
    public function frameworkMeta(): array
    {
        return ['name' => 'NovaCore', 'version' => $this->frameworkVersion, 'modules' => count($this->modules)];
    }

    /** @return array<string,mixed> */
    private function normalizeManifest(array $manifest, string $modulePath): array
    {
        $manifest['name'] = (string) ($manifest['name'] ?? basename($modulePath));
        $manifest['version'] = (string) ($manifest['version'] ?? '1.0.0');
        $manifest['enabled'] = (bool) ($manifest['enabled'] ?? true);
        $manifest['dependencies'] = array_values(array_filter((array) ($manifest['dependencies'] ?? []), 'is_string'));
        $manifest['providers'] = array_values(array_filter((array) ($manifest['providers'] ?? []), 'is_string'));
        $manifest['priority'] = (int) ($manifest['priority'] ?? 100);
        $manifest['path'] = $modulePath;

        return $manifest;
    }

    /** @param array<string,mixed> $module */
    private function isCompatible(array $module): bool
    {
        foreach ($module['dependencies'] as $dependency) {
            if (!$this->hasModuleNamed($dependency)) {
                $this->compatibilityIssues[] = ['module' => $module['name'], 'depends_on' => $dependency];
                return false;
            }
        }

        return true;
    }

    private function hasModuleNamed(string $name): bool
    {
        foreach (glob($this->path . '/*/module.php') ?: [] as $file) {
            $manifest = require $file;
            if (is_array($manifest) && (string) ($manifest['name'] ?? '') === $name && (bool) ($manifest['enabled'] ?? true) === true) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string,mixed> $module */
    private function registerProviders(array $module): void
    {
        $context = new ModuleContext($this->container, $this->events, $module);
        foreach ($module['providers'] as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }

            $provider = new $providerClass();
            if ($provider instanceof ModuleProviderInterface) {
                $provider->register($context);
            }
        }
    }

    /** @param array<string,mixed> $module */
    private function bootProviders(array $module): void
    {
        $context = new ModuleContext($this->container, $this->events, $module);
        foreach ($module['providers'] as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }

            $provider = new $providerClass();
            if ($provider instanceof ModuleProviderInterface) {
                $provider->boot($context);
            }
        }
    }
}
