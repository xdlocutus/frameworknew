# NovaCore
Production-ready modular PHP framework skeleton.

## Features
- MVC, router, DI container, module autoload
- Root entrypoint hosting compatibility (Apache/Nginx)
- Symfony Console based CLI (`php novacore`)
- API + web routes, .env config
- Starter modules and deployment templates

## Install
1. `cp .env.example .env`
2. `composer install`
3. `php novacore make:module Billing`
4. Point webroot to project root

## Deploy
- `git pull`
- `composer install --no-dev --optimize-autoloader`
- `php novacore cache:build`
