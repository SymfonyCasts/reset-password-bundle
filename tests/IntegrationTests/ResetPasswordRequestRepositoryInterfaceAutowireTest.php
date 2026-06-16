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
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\ResetPasswordTestKernel;

final class ResetPasswordRequestRepositoryInterfaceAutowireTest extends TestCase
{
    public function testResetPasswordRequestRepositoryInterfaceIsAutowiredByContainer(): void
    {
        $builder = new ContainerBuilder();
        $builder->autowire(ResetPasswordRequestRepositoryAutowireTest::class)
            ->setPublic(true)
        ;

        $kernel = new ResetPasswordTestKernel($builder);
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get(ResetPasswordRequestRepositoryAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }
}

/**
 * @internal
 */
final class ResetPasswordRequestRepositoryAutowireTest
{
    public function __construct(ResetPasswordRequestRepositoryInterface $repository)
    {
    }
}
