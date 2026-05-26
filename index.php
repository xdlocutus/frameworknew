<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/helpers.php';

use Core\Bootstrap\Application;

$app = Application::boot(basePath: __DIR__);
$app->run();
