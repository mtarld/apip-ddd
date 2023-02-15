<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    if ('dev' === $routingConfigurator->env()) {
        $routingConfigurator->import('@FrameworkBundle/Resources/config/routing/errors.xml')
            ->prefix('/_error');
    }
};
