<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Clock\Clock;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * Trait can be added to a Doctrine ORM repository to help implement
 * ResetPasswordRequestRepositoryInterface.
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
trait ResetPasswordRequestRepositoryTrait
{
    public function getUserIdentifier(object $user): string
    {
        return (string) $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user)
        ;
    }

    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->getEntityManager()->persist($resetPasswordRequest);
        $this->getEntityManager()->flush();
    }

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        $builder = $this->setUserParam($this->createQueryBuilder('t'), $user);

        // Normally there is only 1 max request per use, but written to be flexible
        /** @var ResetPasswordRequestInterface $resetPasswordRequest */
        $resetPasswordRequest = $builder
            ->where('t.user = :user')
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null !== $resetPasswordRequest && !$resetPasswordRequest->isExpired()) {
            return $resetPasswordRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $builder = $this->setUserParam($this->createQueryBuilder('t'), $resetPasswordRequest->getUser());

        $builder
            ->delete()
            ->where('t.user = :user')
            ->getQuery()
            ->execute()
        ;
    }

    public function removeExpiredResetPasswordRequests(): int
    {
        $time = Clock::get()->now()->modify('-1 week');
        $query = $this->createQueryBuilder('t')
            ->delete()
            ->where('t.expiresAt <= :time')
            ->setParameter('time', $time)
            ->getQuery()
        ;

        return $query->execute();
    }

    /**
     * Remove a users ResetPasswordRequest objects from persistence.
     *
     * Warning - This is a destructive operation. Calling this method
     * may have undesired consequences for users who have valid
     * ResetPasswordRequests but have not "checked their email" yet.
     *
     * @see https://github.com/SymfonyCasts/reset-password-bundle?tab=readme-ov-file#advanced-usage
     */
    public function removeRequests(object $user): void
    {
        $builder = $this->setUserParam($this->createQueryBuilder('t'), $user)
            ->delete()
            ->where('t.user = :user')
        ;

        $builder->getQuery()->execute();
    }

    private function setUserParam(QueryBuilder $queryBuilder, object $user): QueryBuilder
    {
        $meta = $this->getEntityManager()->getClassMetadata($user::class);
        $identifier = PropertyAccess::createPropertyAccessor()->getValue($user, $meta->getSingleIdentifierFieldName());

        if ($identifier instanceof Ulid) {
            $queryBuilder->setParameter('user', $identifier, 'ulid');
        } elseif ($identifier instanceof Uuid) {
            $queryBuilder->setParameter('user', $identifier, 'uuid');
        } else {
            $queryBuilder->setParameter('user', $user);
        }

        return $queryBuilder;
    }
}
