<?php

declare(strict_types=1);

namespace Core\Console;

use Core\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kernel extends Application
{
    public function __construct(private readonly Container $container, private readonly string $basePath)
    {
        parent::__construct('NovaCore', '1.0.1');

        $this->add(new MakeModuleCommand($this->basePath));
        $this->add(new CacheBuildCommand($this->basePath));
        $this->add(new CacheClearCommand($this->basePath));
    }
}

final class MakeModuleCommand extends Command
{
    public function __construct(private readonly string $basePath)
    {
        parent::__construct('make:module');
    }

    protected function configure(): void
    {
        $this->setDescription('Scaffold a NovaCore module');
        $this->addArgument('name', InputArgument::REQUIRED, 'Module name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $base = $this->basePath . '/modules/' . $name;

        foreach (['Controllers','Models','Views','Routes','Services','Middleware','Events','Database/Migrations','Database/Seeders','Config'] as $dir) {
            if (!is_dir($base . '/' . $dir)) {
                mkdir($base . '/' . $dir, 0775, true);
            }
        }

        $moduleFile = $base . '/module.php';
        if (!file_exists($moduleFile)) {
            file_put_contents($moduleFile, "<?php\n\nreturn ['name' => '$name', 'enabled' => true];\n");
        }

        $output->writeln("Module {$name} created at {$base}");
        return Command::SUCCESS;
    }
}

final class CacheBuildCommand extends Command
{
    public function __construct(private readonly string $basePath)
    {
        parent::__construct('cache:build');
    }

    protected function configure(): void
    {
        $this->setDescription('Build framework cache artifacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cacheDir = $this->basePath . '/storage/cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }

        $payload = [
            'built_at' => gmdate(DATE_ATOM),
            'app_env' => $_ENV['APP_ENV'] ?? 'production',
            'app_debug' => $_ENV['APP_DEBUG'] ?? 'false',
        ];

        file_put_contents($cacheDir . '/framework.php', '<?php return ' . var_export($payload, true) . ';');
        $output->writeln('NovaCore cache built successfully.');

        return Command::SUCCESS;
    }
}

final class CacheClearCommand extends Command
{
    public function __construct(private readonly string $basePath)
    {
        parent::__construct('cache:clear');
    }

    protected function configure(): void
    {
        $this->setDescription('Clear framework cache artifacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cacheFile = $this->basePath . '/storage/cache/framework.php';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $output->writeln('NovaCore cache cleared.');
        return Command::SUCCESS;
    }
}
