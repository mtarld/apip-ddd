<?php

declare(strict_types=1);

use App\BookStore\Infrastructure\Doctrine\DoctrineAuthorRepository;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookViewFinder;
use App\BookStore\Infrastructure\Doctrine\DoctrineCategoryRepository;
use App\BookStore\Infrastructure\Doctrine\DoctrineOrderRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    foreach ([
        DoctrineAuthorRepository::class,
        DoctrineBookRepository::class,
        DoctrineBookViewFinder::class,
        DoctrineCategoryRepository::class,
        DoctrineOrderRepository::class,
    ] as $adapter) {
        $services->set($adapter)->public();
    }
};
