<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony;

use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\AsEventListener;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(\sprintf('%s/config/{packages}/*.{php,yaml}', $this->getProjectDir()));
        $container->import(\sprintf('%s/config/{packages}/%s/*.{php,yaml}', $this->getProjectDir(), $this->environment));

        $container->import(\sprintf('%s/config/{services}/*.php', $this->getProjectDir()));
        $container->import(\sprintf('%s/config/{services}/%s/*.php', $this->getProjectDir(), $this->environment));
    }

    #[\Override]
    protected function build(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'command.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsEventListener::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'event.bus']);
        });
    }

    /**
     * @return string[]
     *
     * @phpstan-ignore method.unused (invoked by Symfony's KernelTrait::getKernelParameters())
     */
    private function getAllowedEnvs(): array
    {
        return ['dev', 'test', 'prod'];
    }
}
