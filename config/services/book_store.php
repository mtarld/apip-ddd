<?php

declare(strict_types=1);

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\AnonymizeBooksProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\BookCrudProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\DiscountBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\BookCrudProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\CheapestBooksProvider;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\BookStore\\', __DIR__.'/../../src/BookStore');

    // providers
    $services->set(CheapestBooksProvider::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_provider', ['priority' => 1]);

    $services->set(BookCrudProvider::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_provider', ['priority' => 0]);

    // processors
    $services->set(AnonymizeBooksProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 1]);

    $services->set(DiscountBookProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 1]);

    $services->set(BookCrudProcessor::class)
        ->autoconfigure(false)
        ->tag('api_platform.state_processor', ['priority' => 0]);

    // repositories
    $services->set(BookRepositoryInterface::class)
        ->class(DoctrineBookRepository::class);
};
