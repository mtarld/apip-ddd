<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@SyliusBootstrapAdminUiBundle/config/app.php');

    // $containerConfigurator->import('../sylius/twig_hooks/**/**.php');
};
