<?php
namespace Core\Routing;
class Router { private array $routes=[]; public function add(string $method,string $path,callable|array $handler): void {$this->routes[strtoupper($method).$path]=$handler;} public function dispatch(string $method,string $path): mixed { return $this->routes[strtoupper($method).$path] ?? null; }}
