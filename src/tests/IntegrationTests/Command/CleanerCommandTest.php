<?php

namespace SymfonyCasts\Bundle\tests\IntegrationTests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class CleanerCommandTest extends KernelTestCase
{
    public function testGarbageCollection(): void
    {
        //@TODO Remove skip after setting up integration tests
        $this->markTestIncomplete('IntegrationTests not setup yet.');

        $kernel = static::createKernel(['environment' => 'test']);
        $application = new Application($kernel);

        $command = $application->find('reset-password:remove-expired');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Removed 1 reset password request objects.', $output);
    }
}
