<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordTestFixtureUserRepository
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function findAll(): array
    {
        $persister = $this->manager->getUnitOfWork()->getEntityPersister(ResetPasswordTestFixtureUser::class);

        return $persister->loadAll();
    }
}
