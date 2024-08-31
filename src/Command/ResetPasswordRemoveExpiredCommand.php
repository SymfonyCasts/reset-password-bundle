<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
#[AsCommand(name: 'reset-password:remove-expired', description: 'Remove expired reset password requests from persistence.')]
final class ResetPasswordRemoveExpiredCommand extends Command
{
    public function __construct(
        private ResetPasswordCleaner $cleaner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Removing expired reset password requests...');

        $intRemoved = $this->cleaner->handleGarbageCollection(true);

        $output->writeln(\sprintf('Garbage collection successful. Removed %s reset password request object(s).', $intRemoved));

        return 0;
    }
}
