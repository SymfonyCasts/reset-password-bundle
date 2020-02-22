<?php

namespace SymfonyCasts\Bundle\ResetPassword\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class SymfonyCastsResetPasswordExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('reset_password_services.xml');


        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $helperDefinition = $container->getDefinition('symfonycasts.reset_password.helper');
        $helperDefinition->replaceArgument(1, new Reference($config['request_password_repository']));
        $helperDefinition->replaceArgument(2, $config['lifetime']);
        $helperDefinition->replaceArgument(3, $config['throttle_limit']);

        $cleanerDefinition = $container->getDefinition('symfonycasts.reset_password.cleaner');
        $cleanerDefinition->replaceArgument(1, new Reference($config['request_password_repository']));
        $cleanerDefinition->replaceArgument(2, $config['enable_garbage_collection']);
    }
}
