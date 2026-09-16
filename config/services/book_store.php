<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $dir = \dirname(__DIR__, 2).'/src/BookStore';

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\BookStore\\', $dir)
        ->exclude([
            $dir.'/Domain/{Event,Exception,Model,ValueObject}',
            $dir.'/Application/Command/*Command.php',
            $dir.'/Infrastructure/ApiPlatform/Payload',
        ]);
};
