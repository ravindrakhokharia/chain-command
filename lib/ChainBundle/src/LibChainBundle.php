<?php

namespace Lib\ChainBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * [Description LibChainBundle]
 */
class LibChainBundle extends AbstractBundle
{
    /**
     * @param DefinitionConfigurator $definition
     * 
     * @return void
     */
    public function configure(DefinitionConfigurator $definition) : void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * 
     * @return void
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
        $container->import('../config/services.php');

        $container->parameters()->set('lib_chain.chains', $config['chains']);
    }
}
