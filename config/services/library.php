<?php

declare(strict_types=1);

use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Infrastructure\Library\ApiPlatform\State\Processor\AnonymizeBooksProcessor;
use App\Infrastructure\Library\ApiPlatform\State\Processor\BookCrudProcessor;
use App\Infrastructure\Library\ApiPlatform\State\Provider\BookCrudProvider;
use App\Infrastructure\Library\ApiPlatform\State\Processor\DiscountBookProcessor;
use App\Infrastructure\Library\ApiPlatform\State\Provider\CheapestBooksProvider;
use App\Infrastructure\Library\Doctrine\DoctrineBookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Domain\\Library\\', __DIR__.'/../../src/Domain/Library');
    $services->load('App\\Application\\Library\\', __DIR__.'/../../src/Application/Library');
    $services->load('App\\Infrastructure\\Library\\', __DIR__.'/../../src/Infrastructure/Library');

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

