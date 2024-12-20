<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('sylius.routing.loader.crud_routes_attributes', 'service');

    $routingConfigurator->import('sylius.routing.loader.routes_attributes', 'service');
};
