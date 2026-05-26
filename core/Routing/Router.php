<?php

declare(strict_types=1);

namespace Core\Routing;

final class Router
{
    /** @var array<string, callable|array> */
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);
        $this->routes[$this->routeKey($method, $path)] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);

        return $this->routes[$this->routeKey($method, $path)] ?? null;
    }

    public function hasAnyMethodForPath(string $path): bool
    {
        $path = $this->normalizePath($path);
        $suffix = ':' . $path;

        foreach (array_keys($this->routes) as $key) {
            if (str_ends_with($key, $suffix)) {
                return true;
            }
        }

        return false;
    }

    private function routeKey(string $method, string $path): string
    {
        return $method . ':' . $path;
    }

    private function normalizePath(string $path): string
    {
        $normalized = '/' . trim($path, '/');

        return $normalized === '/'
            ? '/'
            : rtrim($normalized, '/');
    }
}
