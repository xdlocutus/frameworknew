<?php
namespace Core\Http;
use Core\Container\Container;use Core\Routing\Router;
class Kernel { public function __construct(private Container $c, private string $basePath){} public function handle(): void { if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); } $router = new Router(); require $this->basePath.'/routes/web.php'; require $this->basePath.'/routes/api.php'; $path=parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; $handler=$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET',$path); if (is_callable($handler)){ echo $handler(); return;} http_response_code(404); echo 'NovaCore 404'; }}
