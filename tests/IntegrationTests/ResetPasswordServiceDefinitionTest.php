<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\AbstractResetPasswordTestKernel;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
final class ResetPasswordServiceDefinitionTest extends TestCase
{
    public function bundleServiceDefinitionDataProvider(): \Generator
    {
        $prefix = 'symfonycasts.reset_password.';

        yield [$prefix.'fake_request_repository'];
        yield [$prefix.'cleaner'];
        yield [$prefix.'random_generator'];
        yield [$prefix.'token_generator'];
        yield [$prefix.'helper'];
        yield ['SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand'];
    }

    /**
     * @dataProvider bundleServiceDefinitionDataProvider
     */
    public function testBundleServiceDefinitions(string $definition): void
    {
        $pass = new CompilerPassForTest();
        $pass->definition = $definition;

        $kernel = new ResetPasswordDefinitionTestKernel();
        $kernel->compilerPass = $pass;
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get($definition);

        $this->expectNotToPerformAssertions();
    }
}

class CompilerPassForTest implements CompilerPassInterface
{
    public $definition;

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition($this->definition)
            ->setPublic(true)
        ;
    }
}

class ResetPasswordDefinitionTestKernel extends AbstractResetPasswordTestKernel
{
    public $compilerPass;

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass($this->compilerPass);
    }
}
