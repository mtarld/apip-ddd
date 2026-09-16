<?php

declare(strict_types=1);

// Hands PHPStan the real entity manager instead of letting phpstan-doctrine rebuild the mapping
// on its own

use App\Shared\Infrastructure\Symfony\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

// The kernel does not read .env on its own, that is the front controller's job. Without this the
// analysis works wherever DATABASE_URL happens to be exported and fails everywhere else.
new Dotenv()->bootEnv(__DIR__.'/../.env');

$kernel = new Kernel('dev', true);
$kernel->boot();

/** @var ManagerRegistry $registry */
$registry = $kernel->getContainer()->get('doctrine');

return $registry->getManager();
