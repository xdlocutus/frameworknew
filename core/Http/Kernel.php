<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Container\Container;
use Core\Routing\Router;
use Throwable;

final class Kernel
{
    public function __construct(
        private readonly Container $container,
        private readonly string $basePath,
    ) {}

    public function handle(): void
    {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $router = new Router();
            require $this->basePath . '/routes/web.php';
            require $this->basePath . '/routes/api.php';

            $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
            $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
            $handler = $router->dispatch($method, $path);

            if ($handler === null) {
                if ($router->hasAnyMethodForPath($path)) {
                    $this->respond('Method Not Allowed', 405, $this->isApiRequest($path));
                    return;
                }

                $this->respond('Not Found', 404, $this->isApiRequest($path));
                return;
            }

            if (is_callable($handler)) {
                $response = $handler();
                if ($response !== null) {
                    echo $response;
                }
            }
        } catch (Throwable $exception) {
            error_log(sprintf('[NovaCore] Unhandled exception: %s in %s:%d', $exception->getMessage(), $exception->getFile(), $exception->getLine()));
            $this->respond('Internal Server Error', 500, $this->isApiRequest());
        }
    }

    private function isApiRequest(?string $path = null): bool
    {
        $requestPath = $path ?? (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');

        return str_starts_with($requestPath, '/api/');
    }

    private function respond(string $message, int $status, bool $asJson): void
    {
        http_response_code($status);

        if ($asJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => $status, 'error' => $message], JSON_UNESCAPED_SLASHES);
            return;
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo 'NovaCore ' . $status . ': ' . $message;
    }
}
