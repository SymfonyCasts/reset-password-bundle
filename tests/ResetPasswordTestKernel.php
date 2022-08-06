<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
class ResetPasswordTestKernel extends Kernel
{
    private $builder;
    private $routes;
    private $extraBundles;

    /**
     * @param array             $routes  Routes to be added to the container e.g. ['name' => 'path']
     * @param BundleInterface[] $bundles Additional bundles to be registered e.g. [new Bundle()]
     */
    public function __construct(ContainerBuilder $builder = null, array $routes = [], array $bundles = [])
    {
        $this->builder = $builder;
        $this->routes = $routes;
        $this->extraBundles = $bundles;

        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return array_merge(
            $this->extraBundles,
            [
                new FrameworkBundle(),
                new SymfonyCastsResetPasswordBundle(),
                new DoctrineBundle(),
            ]
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        if (null === $this->builder) {
            $this->builder = new ContainerBuilder();
        }

        $builder = $this->builder;

        $loader->load(function (ContainerBuilder $container) use ($builder) {
            $container->merge($builder);
            $container->loadFromExtension(
                'framework',
                [
                    'secret' => 'foo',
                    'router' => [
                        'resource' => 'kernel::loadRoutes',
                        'type' => 'service',
                        'utf8' => true,
                    ],
                    'http_method_override' => false,
                ]
            );

            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'url' => 'sqlite:///'.$this->getCacheDir().'/app.db',
                ],
                'orm' => [
                    'auto_generate_proxy_classes' => true,
                    'auto_mapping' => true,
                    'mappings' => [
                        'App' => [
                            'is_bundle' => false,
                            'type' => 'annotation',
                            'dir' => 'tests/Fixtures/Entity/',
                            'prefix' => 'SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity',
                            'alias' => 'App',
                        ],
                    ],
                ],
            ]);

            $container->register(ResetPasswordTestFixtureRequestRepository::class)
                ->setAutoconfigured(true)
                ->setAutowired(true)
            ;

            $container->loadFromExtension('symfonycasts_reset_password', [
                'request_password_repository' => ResetPasswordTestFixtureRequestRepository::class,
            ]);

            $container->register('kernel', static::class)
                ->setPublic(true)
            ;

            $kernelDefinition = $container->getDefinition('kernel');
            $kernelDefinition->addTag('routing.route_loader');
        });
    }

    /**
     * @internal
     */
    public function loadRoutes(LoaderInterface $loader): RouteCollection
    {
        $routes = new RouteCollection();

        foreach ($this->routes as $name => $path) {
            $routes->add($name, new Route($path));
        }

        return $routes;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/cache'.spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/logs'.spl_object_hash($this);
    }
}
