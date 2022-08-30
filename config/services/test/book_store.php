<?php

declare(strict_types=1);

use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\InMemory\InMemoryBookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // repositories
    $services->set(BookRepositoryInterface::class)
        ->class(InMemoryBookRepository::class);
};
