<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Log\Logger;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
class AbstractResetPasswordTestKernel extends Kernel
{
    use MicroKernelTrait;

    private $cacheDir;

    private $logDir;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SymfonyCastsResetPasswordBundle(),
            new DoctrineBundle(),
        ];
    }

    public function getCacheDir()
    {
        if (null === $this->cacheDir) {
            return \sys_get_temp_dir().'/cache'.\spl_object_hash($this);
        }

        return $this->cacheDir;
    }

    public function getLogDir()
    {
        if (null === $this->logDir) {
            return \sys_get_temp_dir().'/logs'.\spl_object_hash($this);
        }

        return $this->logDir;
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->loadFromExtension('framework', [
            'secret' => 'foo',
            'router' => [
                'utf8' => true,
            ],
        ]);
        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'url' => 'sqlite:///'.$this->getCacheDir().'/app.db',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'App' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/tests/Fixtures/Entity/',
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

        // avoid logging request logs
        $container->register('logger', Logger::class)
            ->setArgument(0, LogLevel::EMERGENCY);
    }
}
