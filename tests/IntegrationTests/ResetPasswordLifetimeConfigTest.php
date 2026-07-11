<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\ResetPasswordTestKernel;

/**
 * @author Pascal Cescon <pascal.cescon@gmail.com>
 */
final class ResetPasswordLifetimeConfigTest extends TestCase
{
    public function testDefaultLifetimeIsUsedByTheHelper(): void
    {
        $helper = $this->bootKernelAndGetHelper(new ResetPasswordTestKernel($this->createBuilder()));

        self::assertSame(3600, $helper->getTokenLifetime());
    }

    public function testLifetimeConfiguredInUserlandIsUsedByTheHelper(): void
    {
        $helper = $this->bootKernelAndGetHelper(new ResetPasswordLifetimeTestKernel($this->createBuilder()));

        self::assertSame(ResetPasswordLifetimeTestKernel::LIFETIME, $helper->getTokenLifetime());
    }

    private function createBuilder(): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        $builder->autowire(ResetPasswordHelperHolder::class)
            ->setPublic(true)
        ;

        return $builder;
    }

    private function bootKernelAndGetHelper(ResetPasswordTestKernel $kernel): ResetPasswordHelperInterface
    {
        $kernel->boot();

        return $kernel->getContainer()->get(ResetPasswordHelperHolder::class)->helper;
    }
}

/**
 * Boots the test kernel with a "lifetime" explicitly configured, as an app would do.
 *
 * @internal
 */
final class ResetPasswordLifetimeTestKernel extends ResetPasswordTestKernel
{
    public const LIFETIME = 9999;

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(static function (ContainerBuilder $container) {
            $container->loadFromExtension('symfonycasts_reset_password', ['lifetime' => self::LIFETIME]);
        });
    }
}

/**
 * The helper is a private service - autowiring it into a public service is the
 * only way to get a hold of it from the container.
 *
 * @internal
 */
final class ResetPasswordHelperHolder
{
    public function __construct(public readonly ResetPasswordHelperInterface $helper)
    {
    }
}
