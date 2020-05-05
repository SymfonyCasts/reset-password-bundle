<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\ResetPasswordTestKernel;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class ResetPasswordInterfaceAutowireTest extends TestCase
{
    public function testResetPasswordInterfaceIsAutowiredByContainer(): void
    {
        $builder = new ContainerBuilder();
        $builder->autowire(ResetPasswordAutowireTest::class)
            ->setPublic(true)
        ;

        $kernel = new ResetPasswordTestKernel($builder);
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get(ResetPasswordAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }
}

/**
 * @internal
 */
final class ResetPasswordAutowireTest
{
    public function __construct(ResetPasswordHelperInterface $helper)
    {
    }
}
