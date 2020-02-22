<?php

namespace SymfonyCasts\Bundle\ResetPassword\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
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
                    ->info('The length of time in seconds that a password reset request is valid for after it is created.')
                ->end()
                ->integerNode('throttle_limit')
                    ->defaultValue(3600)
                    ->info('Another password reset cannot be made faster than this throttle time in seconds.')
                ->end()
                ->booleanNode('enable_garbage_collection')
                    ->defaultValue(true)
                    ->info('') //@TODO Something goes here
            ->end();

        return $treeBuilder;
    }
}
