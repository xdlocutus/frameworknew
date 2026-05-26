<?php
namespace Core\Bootstrap;
use Dotenv\Dotenv;
final class Environment { public function __construct(private string $basePath) {} public function load(): void { if (file_exists($this->basePath.'/.env')) Dotenv::createImmutable($this->basePath)->safeLoad(); }}
