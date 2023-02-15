<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'router' => [
            'utf8' => true,
        ],
    ]);
    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'router' => [
                'strict_requirements' => null,
            ],
        ]);
    }
};
