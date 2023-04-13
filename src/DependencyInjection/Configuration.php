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
                ->scalarNode("default_language")->defaultValue('en')->cannotBeEmpty()->info('Define default language.')->end()
                ->scalarNode("git_email")->defaultValue('anonym@email.com')->cannotBeEmpty()->info('Define your email with which commits with messages will be done.')->end()
                ->scalarNode("git_name")->defaultValue('anonym')->cannotBeEmpty()->info('Define your name with which commits with messages will be done')->end()
                ->scalarNode("git_access_token")->defaultValue('accessToken')->cannotBeEmpty()->info('Access token generated by GitHub you can make one here (https://github.com/settings/tokens)')->end()
            ->end();

        return $treeBuilder;
    }
}