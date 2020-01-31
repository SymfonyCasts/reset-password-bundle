<?php

namespace SymfonyCasts\Bundle\ResetPassword\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('symfonycasts_reset_password');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('password_request_repository')
                    ->isRequired()
                    ->info('A class that implements PasswordResetRequestRepositoryInterface - usually your PasswordResetTokenRepository.')
                ->end()
                ->integerNode('lifetime')
                    ->defaultValue(3600)
                    ->info('How long a reset password token should be valid before expiring.')
                ->end()
                ->integerNode('request_throttle_time')
                    ->defaultValue(3600)
                    ->info('How long a reset password token should be valid before expiring.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}