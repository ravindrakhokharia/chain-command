<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            ->set('bar.command.hi_command', 'Lib\BarBundle\Command\HiCommand')
            ->tag('console.command')
    ;
};
