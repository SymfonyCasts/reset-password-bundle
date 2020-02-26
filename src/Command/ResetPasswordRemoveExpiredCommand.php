<?php

namespace SymfonyCasts\Bundle\ResetPassword\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
class ResetPasswordRemoveExpiredCommand extends Command
{
    protected static $defaultName = 'reset-password:remove-expired';

    private $cleaner;

    public function __construct(ResetPasswordCleaner $cleaner)
    {
        $this->cleaner = $cleaner;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setDescription('Remove expired reset password requests from persistence.');
    }

    /**
     * @inheritDoc
     * @psalm-suppress InvalidReturnType
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Removing expired reset password requests...');

        $intRemoved = $this->cleaner->handleGarbageCollection(true);

        $output->writeln(\sprintf('Garbage collection successful. Removed %s reset password request objects.', $intRemoved));
    }
}
