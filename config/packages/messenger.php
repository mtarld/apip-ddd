<?php

declare(strict_types=1);

use App\Application\Shared\Command\CommandInterface;
use App\Application\Shared\Query\QueryInterface;
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
            'reset_on_message' => true,
        ],
    ]);
};
