<?php

declare(strict_types=1);

use App\Shared\Application\Event\EventBusInterface;
use App\Tests\Shared\Spy\EventBusSpy;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(EventBusSpy::class)
        ->decorate(EventBusInterface::class)
        ->public();
};
