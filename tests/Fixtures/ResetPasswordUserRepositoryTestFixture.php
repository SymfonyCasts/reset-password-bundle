<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordUserTestFixture;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordUserRepositoryTestFixture
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function findAll(): array
    {
        $persister = $this->manager->getUnitOfWork()->getEntityPersister(ResetPasswordUserTestFixture::class);

        return $persister->loadAll();
    }
}
