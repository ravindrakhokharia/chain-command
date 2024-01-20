<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->set('chain.logger.formatter', 'Monolog\Formatter\LineFormatter')
            ->args(["[%%datetime%%] %%message%%\n", "Y-m-d H:i:s"])
    ;

    $services->alias('chain.command.logger', service('monolog.logger.chain_command'));

    $services
        ->set('chain.command.manager', 'Lib\ChainBundle\Manager\ChainCommandManager')
            ->args(['%lib_chain.chains%'])
    ;

    $services
        ->set('chain.subscriber.console_command_subscriber', 'Lib\ChainBundle\EventSubscriber\ConsoleCommandSubscriber')
            ->arg('$chainCommandLogger', service('chain.command.logger'))
            ->arg('$chainCommandManager', service('chain.command.manager'))
            ->tag('kernel.event_subscriber')
    ;
};
