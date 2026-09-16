<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\run;

#[AsTask(description: 'Enter the application container')]
function shell(): void
{
    run('docker compose exec php sh', context: context()->withTty());
}

#[AsTask(description: 'Install the whole dev environment')]
function install(): void
{
    run('docker compose build');
    start();
    run('docker compose exec php composer install --optimize-autoloader');
    db_reset();
}

#[AsTask(description: 'Start the project')]
function start(): void
{
    run('docker compose up -d --remove-orphans --no-recreate');
}

#[AsTask(description: 'Stop the project')]
function stop(): void
{
    run('docker compose stop');
    run('docker compose rm -v --force');
}

#[AsTask(name: 'db:shell', description: 'Enter the database container')]
function db_shell(): void
{
    run('docker compose exec database psql -Uapp app', context: context()->withTty());
}

#[AsTask(name: 'db:create', description: 'Create/Recreate the database')]
function db_create(): void
{
    run('docker compose exec php bin/console doctrine:database:drop --force --if-exists -nq');
    run('docker compose exec php bin/console doctrine:database:create -nq');
}

#[AsTask(name: 'db:update', description: 'Update database schema')]
function db_update(): void
{
    run('docker compose exec php bin/console doctrine:schema:update --force -nq');
}

#[AsTask(name: 'db:reset', description: 'Reset database')]
function db_reset(): void
{
    db_create();
    db_update();
}

#[AsTask(name: 'check:rector', description: 'Check code with Rector')]
function check_rector(): void
{
    run('docker compose exec php vendor/bin/rector process --dry-run');
}

#[AsTask(name: 'check:cs', description: 'Check code style with PHP-CS-Fixer')]
function check_cs(): void
{
    run('docker compose exec php vendor/bin/php-cs-fixer fix --dry-run --diff');
}

#[AsTask(name: 'check:phpstan', description: 'Run PHPStan static analysis')]
function check_phpstan(): void
{
    run('docker compose exec php vendor/bin/phpstan analyse --memory-limit=512M');
}

#[AsTask(name: 'check:deptrac', description: 'Check architecture with Deptrac')]
function check_deptrac(): void
{
    io()->section('Checking Bounded contexts...');
    run('docker compose exec php vendor/bin/deptrac analyse -c deptrac_bc.yaml --fail-on-uncovered --report-uncovered --no-progress');

    io()->section('Checking Hexagonal layers...');
    run('docker compose exec php vendor/bin/deptrac analyse -c deptrac_hexa.yaml --fail-on-uncovered --report-uncovered --no-progress');
}

#[AsTask(description: 'Run PHPUnit tests')]
function test(): void
{
    run('docker compose exec php bin/phpunit');
}

#[AsTask(description: 'Run all checks and tests')]
function ci(): void
{
    check_rector();
    check_cs();
    check_phpstan();
    check_deptrac();
    test();
}

#[AsTask(name: 'fix:rector', description: 'Apply Rector refactorings')]
function fix_rector(): void
{
    run('docker compose exec php vendor/bin/rector process');
}

#[AsTask(name: 'fix:cs', description: 'Fix code style with PHP-CS-Fixer')]
function fix_cs(): void
{
    run('docker compose exec php vendor/bin/php-cs-fixer fix');
}
