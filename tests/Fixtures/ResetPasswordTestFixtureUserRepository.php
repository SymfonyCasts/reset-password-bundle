<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 *
 * @method ResetPasswordTestFixtureUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPasswordTestFixtureUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPasswordTestFixtureUser[]    findAll()
 * @method ResetPasswordTestFixtureUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ResetPasswordTestFixtureUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordTestFixtureUser::class);
    }
}
