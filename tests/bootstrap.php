<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Symfony\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Dotenv\Dotenv;

require \dirname(__DIR__).'/vendor/autoload.php';

if (\file_exists(\dirname(__DIR__).'/config/bootstrap.php')) {
    require \dirname(__DIR__).'/config/bootstrap.php';
} elseif (\method_exists(Dotenv::class, 'bootEnv')) {
    new Dotenv()->bootEnv(\dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    \umask(0o000);
}

$kernel = new Kernel('test', (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

$application->find('doctrine:database:create')->run(new ArrayInput(['--if-not-exists' => true]), new NullOutput());
$application->find('doctrine:schema:update')->run(new ArrayInput(['--force' => true]), new NullOutput());

$kernel->shutdown();
