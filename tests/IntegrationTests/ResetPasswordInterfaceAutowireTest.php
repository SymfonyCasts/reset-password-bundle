<?php

namespace SymfonyCasts\Bundle\ResetPassword\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\AbstractResetPasswordTestKernel;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordInterfaceAutowireTest extends TestCase
{
    public function testResetPasswordInterfaceIsAutowiredByContainer(): void
    {
        $kernel = new ResetPasswordIntegrationKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $container->get(ResetPasswordAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }
}

class ResetPasswordIntegrationKernel extends AbstractResetPasswordTestKernel
{
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        parent::configureContainer($container, $loader);

        $container->autowire(ResetPasswordAutowireTest::class)
            ->setPublic(true)
        ;
    }
}

class ResetPasswordAutowireTest
{
    public function __construct(ResetPasswordHelperInterface $helper)
    {
    }
}
