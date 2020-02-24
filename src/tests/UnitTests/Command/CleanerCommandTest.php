<?php

namespace SymfonyCasts\Bundle\tests\UnitTests\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SymfonyCasts\Bundle\ResetPassword\Command\CleanerCommand;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class CleanerCommandTest extends TestCase
{
    public function testCommandCallsForcedGarbageCollection(): void
    {
        $mockCleaner = $this->createMock(ResetPasswordCleaner::class);
        $mockCleaner
            ->expects($this->once())
            ->method('handleGarbageCollection')
            ->with(true)
        ;

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $command = $this->getCommandFixture($mockCleaner);
        $command->callExecute($input, $output);
    }

    private function getCommandFixture($mockCleaner)
    {
        return new class ($mockCleaner) extends CleanerCommand
        {
            public function callExecute(InputInterface $input, OutputInterface $output): void
            {
                $this->execute($input, $output);
            }
        };
    }
}
