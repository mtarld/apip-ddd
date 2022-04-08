<?php

declare(strict_types=1);

use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\AnonymizeBooksProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\BookCrudProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Provider\BookCrudProvider;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\DiscountBookProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Provider\CheapestBooksProvider;
use App\Infrastructure\BookStore\Doctrine\DoctrineBookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Domain\\BookStore\\', __DIR__.'/../../src/Domain/BookStore');
    $services->load('App\\Application\\BookStore\\', __DIR__.'/../../src/Application/BookStore');
    $services->load('App\\Infrastructure\\BookStore\\', __DIR__.'/../../src/Infrastructure/BookStore');

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

