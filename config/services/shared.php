<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $dir = \dirname(__DIR__, 2).'/src/Shared';

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Shared\\', $dir)
        ->exclude([
            $dir.'/Domain',
            $dir.'/Infrastructure/ApiPlatform/State/Paginator.php',
            $dir.'/Infrastructure/Symfony/Kernel.php',
        ]);
};
