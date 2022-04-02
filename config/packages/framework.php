<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'framework',
        [
            'secret' => '%env(APP_SECRET)%',
            'http_method_override' => false,
            'php_errors' => ['log' => 4096],
        ]
    );
};
