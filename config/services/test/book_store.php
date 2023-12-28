<?php

declare(strict_types=1);

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    // repositories
    $services->set(BookRepositoryInterface::class)
        ->class(InMemoryBookRepository::class);

    $services->set(InMemoryBookRepository::class)
        ->public();

    $services->set(DoctrineBookRepository::class)
        ->public();
};
