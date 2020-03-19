<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordRequestTestFixture;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordRequestRepositoryTestFixture implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    private $manager;

    public function __construct(EntityManagerInterface $manager = null)
    {
        $this->manager = $manager;
    }

    public function createResetPasswordRequest(
        object $user,
        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ): ResetPasswordRequestInterface {
    }

    public function findOneBy(array $criteria)
    {
        $persister = $this->manager->getUnitOfWork()->getEntityPersister(ResetPasswordRequestTestFixture::class);

        return $persister->load($criteria);
    }

    public function findAll()
    {
        $persister = $this->manager->getUnitOfWork()->getEntityPersister(ResetPasswordRequestTestFixture::class);

        return $persister->loadAll();
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    private function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return $this->manager->createQueryBuilder()
            ->select($alias)
            ->from(ResetPasswordRequestTestFixture::class, $alias, $indexBy)
            ;
    }
}
