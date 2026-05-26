<?php
namespace Core\Modules;
use Core\Container\Container;
class ModuleManager { public function __construct(private string $path, private Container $c){} public function boot(): void { foreach (glob($this->path.'/*/module.php') ?: [] as $f) { $meta=require $f; if (($meta['enabled'] ?? true) && isset($meta['boot']) && is_callable($meta['boot'])) { $meta['boot']($this->c); } } }}
