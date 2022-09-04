<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ClearNativeProviderAndProcessorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach (['api_platform.state_provider', 'api_platform.state_processor'] as $tag) {
            foreach (array_keys($container->findTaggedServiceIds($tag)) as $id) {
                $definition = $container->findDefinition($id);
                $class = $definition->getClass();

                if (null !== $class && !preg_match('/^App\\\/', $class)) {
                    $definition->clearTag($tag);
                }
            }
        }
    }
}
