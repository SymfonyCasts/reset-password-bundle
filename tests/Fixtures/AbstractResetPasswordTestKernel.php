<?php

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
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

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SymfonyCastsResetPasswordBundle()
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->loadFromExtension('framework', [
            'secret' => 'foo',
            'router' => [
                'utf8' => true
            ]
        ]);

        $container->register(ResetPasswordRepositoryTestFixture::class);

        $container->loadFromExtension('symfonycasts_reset_password', [
            'request_password_repository' => ResetPasswordRepositoryTestFixture::class
        ]);
    }

    public function getProjectDir(): string
    {
        return \dirname( __DIR__, 2);
    }
}
