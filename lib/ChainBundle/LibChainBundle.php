<?php

namespace Lib\ChainBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LibChainBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition) : void
    {
        $definition->import('./config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
        $container->import('./config/services.php');

        $container->parameters()->set('lib_chain.chains', $config['chains']);
    }
}
