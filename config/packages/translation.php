<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', config: [
        'default_locale' => param('locale'),
        'translator' => [
            'default_path' => '%kernel.project_dir%/translations',
            'fallbacks' => [
                'fr',
            ],
            'providers' => null,
        ],
    ]);
};
