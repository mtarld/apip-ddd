<?php

declare(strict_types=1);

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'default_bus' => 'command.bus',
            'buses' => [
                'command.bus' => [],
                'query.bus' => [],
            ],
            'transports' => [
                'sync' => 'sync://',
            ],
            'routing' => [
                QueryInterface::class => 'sync',
                CommandInterface::class => 'sync',
            ],
        ],
    ]);
};
