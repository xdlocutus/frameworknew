<?php
function env(string $k,mixed $d=null): mixed { return $_ENV[$k] ?? $_SERVER[$k] ?? $d; }
