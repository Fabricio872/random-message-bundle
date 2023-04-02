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
            ->end();

        return $treeBuilder;
    }
}