<?php

namespace Fabricio872\RandomMessageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RandomMessageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $serviceDefinition = $container->getDefinition('fabricio872_random_message.random_message');
        $serviceDefinition->setArgument(0, $config['path']);

        $createCommandDefinition = $container->getDefinition('fabricio872_random_message.command.random_message_create_command');
        $createCommandDefinition->setArgument(0, $config['path']);
    }
}