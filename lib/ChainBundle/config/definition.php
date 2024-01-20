<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#configuration
 *
 * chain_command:
 *   chains:
 *     parent_command: 'app:foo'
 *     children:
 *       -
 *         command:
 *         args:
 *           arg1: value1
 *           --pass1: false
 */
return static function (DefinitionConfigurator $definition): void {
    $definition
        ->rootNode()
            ->children()
                ->arrayNode('chains')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('parent_command')->end()
                            ->arrayNode('children')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('command')->end()
                                        ->arrayNode('args')
                                            ->normalizeKeys(false)
                                            ->useAttributeAsKey('argv')
                                                ->prototype('scalar')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;
};
