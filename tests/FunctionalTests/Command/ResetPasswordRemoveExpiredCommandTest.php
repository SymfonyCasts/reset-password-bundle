<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\FunctionalTests\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Tester\CommandTester;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\AbstractResetPasswordTestKernel;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordRemoveExpiredCommandTest extends TestCase
{
    /**
     * @var ObjectManager|object
     */
    private $manager;

    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $kernel = new CommandKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var Registry $registry */
        $registry = $container->get('doctrine');
        $this->manager = $registry->getManager();

        $this->configureDatabase();

        /** @var CommandLoaderInterface $loader */
        $loader = $container->get('console.command_loader');
        $this->configureCommandTester($loader);
    }

    public function testRemoveExpiredCommandWithNothingToRemove(): void
    {
        $this->configureDatabase();

        $this->commandTester->execute([]);

        self::assertStringContainsString(
            'Garbage collection successful. Removed 0 reset password request object(s).',
            $this->commandTester->getDisplay()
        );
    }

    public function testRemovedExpiredCommand(): void
    {
        $request = new ResetPasswordTestFixtureRequest();
        $request->expiresAt = new \DateTimeImmutable('-2 hours');

        $this->manager->persist($request);
        $this->manager->flush();

        $this->commandTester->execute([]);

        self::assertStringContainsString(
            'Garbage collection successful. Removed 1 reset password request object(s).',
            $this->commandTester->getDisplay()
        );
    }

    private function configureCommandTester(CommandLoaderInterface $loader): void
    {
        $application = new Application();
        $application->setCommandLoader($loader);

        $command = $application->find('reset-password:remove-expired');
        $this->commandTester = new CommandTester($command);
    }

    private function configureDatabase(): void
    {
        $metaData = $this->manager->getMetadataFactory();

        $tool = new SchemaTool($this->manager);
        $tool->dropDatabase();
        $tool->createSchema($metaData->getAllMetadata());
    }
}

class CommandKernel extends AbstractResetPasswordTestKernel
{
}
