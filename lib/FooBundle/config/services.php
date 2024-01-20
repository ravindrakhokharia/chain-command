<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            ->set('foo.command.hello_command', 'Lib\FooBundle\Command\HelloCommand')
            ->tag('console.command')
    ;
};
