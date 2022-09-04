<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webmozart\Assert\InvalidArgumentException;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/src/BookStore/Infrastructure/ApiPlatform/Resource/',
                '%kernel.project_dir%/src/Subscription/Entity/',
            ],
        ],
        'patch_formats' => [
            'json' => ['application/merge-patch+json'],
        ],
        'swagger' => [
            'versions' => [3],
        ],
        'exception_to_status' => [
            // TODO
            // We must trigger the API Platform validator before the data transforming.
            // Let's create an API Platform PR to update the AbstractItemNormalizer.
            // In that way, this exception won't be raised anymore as payload will be validated (see DiscountBookPayload).
            InvalidArgumentException::class => 422,
        ],
    ]);
};
