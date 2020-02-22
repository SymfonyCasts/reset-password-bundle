<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

class CleanerCommand extends Command
{
    protected static $defaultName = 'reset-password:remove-expired';

    private $cleaner;

    public function __construct(ResetPasswordCleaner $cleaner)
    {
        $this->cleaner = $cleaner;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Remove expired reset password requests from persistence.');
    }

    /**
     * @psalm-suppress InvalidReturnType
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Removing expired reset password requests...');

        $intRemoved = $this->cleaner->handleGarbageCollection(true);

        $output->writeln('Garbage collection successful. Removed '.$intRemoved.' reset password request objects.');
    }
}
