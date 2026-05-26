<?php

declare(strict_types=1);

namespace Core\Modules;

use Core\Container\Container;

final class ModuleManager
{
    /** @var array<int, array<string, mixed>> */
    private array $modules = [];

    public function __construct(private readonly string $path, private readonly Container $container) {}

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

            $this->modules[] = $module;

            $boot = $module['boot'] ?? null;
            if (is_callable($boot)) {
                $boot($this->container);
            }
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

    /** @return array<string,mixed> */
    private function normalizeManifest(array $manifest, string $modulePath): array
    {
        $manifest['name'] = (string) ($manifest['name'] ?? basename($modulePath));
        $manifest['enabled'] = (bool) ($manifest['enabled'] ?? true);
        $manifest['path'] = $modulePath;

        return $manifest;
    }
}
