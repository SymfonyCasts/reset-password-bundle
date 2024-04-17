<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class SymfonyCastsResetPasswordBundle extends AbstractBundle
{
    protected string $extensionAlias = 'symfonycasts_reset_password';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('request_password_repository')
                    ->isRequired()
                    ->info('A class that implements ResetPasswordRequestRepositoryInterface - usually your ResetPasswordRequestRepository.')
                ->end()
                ->integerNode('lifetime')
                    ->defaultValue(3600)
                    ->info('The length of time in seconds that a password reset request is valid for after it is created.')
                ->end()
                ->integerNode('throttle_limit')
                    ->defaultValue(3600)
                    ->info('Another password reset cannot be made faster than this throttle time in seconds.')
                ->end()
                ->booleanNode('enable_garbage_collection')
                    ->defaultValue(true)
                    ->info('Enable/Disable automatic garbage collection.')
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/reset_password_services.xml');

        $container->services()
            ->get('symfonycasts.reset_password.helper')
                ->arg(2, new Reference($config['request_password_repository']))
                ->arg(3, $config['lifetime'])
                ->arg(4, $config['throttle_limit'])
            ->get('symfonycasts.reset_password.cleaner')
                ->arg(0, new Reference($config['request_password_repository']))
                ->arg(1, $config['enable_garbage_collection'])
        ;
    }
}
