<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters
        ->set('locale', 'en')
    ;

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([dirname(__DIR__, 2).'/src/Shared/Infrastructure/Symfony/Kernel.php']);
};
