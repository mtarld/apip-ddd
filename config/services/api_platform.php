<?php

declare(strict_types=1);

use ApiPlatform\Metadata\UriVariablesConverter;
use App\Shared\Infrastructure\ApiPlatform\UriVariable\IdentityPropertyMetadataFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('api_platform.uri_variables.converter', UriVariablesConverter::class)
        ->args([
            inline_service(IdentityPropertyMetadataFactory::class)
                ->args([service('api_platform.metadata.property.metadata_factory')]),
            service('api_platform.metadata.resource.metadata_collection_factory'),
            tagged_iterator('api_platform.uri_variables.transformer'),
        ]);
};
