<?php
namespace Core\Config;
class Config { private array $items=[]; public function __construct(string $path){ foreach (glob($path.'/*.php') ?: [] as $f) $this->items[basename($f,'.php')] = require $f; } public function get(string $key,mixed $default=null): mixed { [$f,$k]=array_pad(explode('.',$key,2),2,null); return $k?($this->items[$f][$k]??$default):($this->items[$f]??$default);} }
