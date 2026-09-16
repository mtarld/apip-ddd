<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // API Platform 4.3's MCP integration wires itself against a single `mcp.registry` service,
    // which is what symfony/mcp-bundle exposed up to 0.12. Since 0.13 it registers one registry
    // per server, so the reference no longer resolves on its own.
    $containerConfigurator->services()
        ->alias('mcp.registry', 'mcp.server.bookstore.registry');
};
