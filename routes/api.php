<?php
$router->add('GET', '/api/v1/health', fn() => json_encode(['status'=>'ok','framework'=>'NovaCore']));
