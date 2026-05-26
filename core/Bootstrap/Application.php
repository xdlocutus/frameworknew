<?php

declare(strict_types=1);

namespace Core\Bootstrap;

use Core\Config\Config;
use Core\Container\Container;
use Core\Console\Kernel as ConsoleKernel;
use Core\Events\EventDispatcher;
use Core\Http\Kernel as HttpKernel;
use Core\Modules\ModuleManager;

final class Application
{
    public function __construct(
        private readonly string $basePath,
        private readonly Container $container,
        private readonly Config $config,
    ) {}

    public static function boot(string $basePath): self
    {
        $container = new Container();
        $config = new Config($basePath . '/config');
        $app = new self($basePath, $container, $config);
        $container->set(self::class, $app);
        $container->set(Container::class, $container);
        $container->set(Config::class, $config);
        $events = new EventDispatcher();
        $container->set(EventDispatcher::class, $events);
        (new Environment($basePath))->load();

        $moduleManager = new ModuleManager($basePath . '/modules', $container, $events, '1.0.0');
        $moduleManager->boot();
        $container->set(ModuleManager::class, $moduleManager);

        return $app;
    }

    public function run(): void
    {
        (new HttpKernel($this->container, $this->basePath))->handle();
    }

    public function console(): ConsoleKernel
    {
        return new ConsoleKernel($this->container, $this->basePath);
    }
}
