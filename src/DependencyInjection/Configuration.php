<?php

namespace SymfonyCasts\Bundle\ResetPassword\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('symfonycasts_reset_password');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('request_password_repository')
                    ->isRequired()
                    ->info('A class that implements ResetPasswordRequestRepositoryInterface - usually your ResetPasswordRequestRepository.')
                ->end()
                ->integerNode('lifetime')
                    ->defaultValue(3600)
                    ->info('How long a reset password token should be valid before expiring.')
                ->end()
                ->integerNode('throttle_limit')
                    ->defaultValue(3600)
                    ->info('Length of time between non-expired reset password requests.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}