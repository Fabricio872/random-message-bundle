<?php

namespace Fabricio872\RandomMessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('random_message');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode("path")->defaultValue('%kernel.project_dir%/var/random_messages')->info('Define default path where list of messages will be stored.')->end()
                ->arrayNode('repositories')
                    ->defaultValue(['https://github.com/Fabricio872/random-message-repository'])
                    ->info('List of repositories for messages')
                        ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}