<?php

declare(strict_types=1);

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
        $serviceDefinition->setArgument(1, $config['default_language']);

        $createCommandDefinition = $container->getDefinition('fabricio872_random_message.command.random_message_create_command');
        $createCommandDefinition->setArgument(0, $config['path']);
        $createCommandDefinition->setArgument(1, $config['repositories']);

        $pullCommandDefinition = $container->getDefinition('fabricio872_random_message.service.git_service');
        $pullCommandDefinition->setArgument(0, $config['path']);

        $pullCommandDefinition = $container->getDefinition('fabricio872_random_message.command.random_message_pull_command');
        $pullCommandDefinition->setArgument(0, $config['path']);
        $pullCommandDefinition->setArgument(1, $config['repositories']);

        $pushCommandDefinition = $container->getDefinition('fabricio872_random_message.command.random_message_push_command');
        $pushCommandDefinition->setArgument(0, $config['path']);
        $pushCommandDefinition->setArgument(1, $config['repositories']);
        $pushCommandDefinition->setArgument(2, $config['git_email']);
        $pushCommandDefinition->setArgument(3, $config['git_name']);
        $pushCommandDefinition->setArgument(4, $config['git_access_token']);
    }
}
