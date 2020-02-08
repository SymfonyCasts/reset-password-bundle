<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
trait ResetPasswordRequestRepositoryTrait
{
    public function getUserIdentifier(object $user): string
    {
        return $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user)
        ;
    }

    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest)
    {
        $this->getEntityManager()->persist($resetPasswordRequest);
        $this->getEntityManager()->flush($resetPasswordRequest);
    }

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        // Normally there is only 1 max request per use, but written to be flexible
        /** @var ResetPasswordRequestInterface $resetPasswordRequest */
        $resetPasswordRequest = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneorNullResult()
        ;

        if (null !== $resetPasswordRequest && !$resetPasswordRequest->isExpired()) {
            return $resetPasswordRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->getEntityManager()->remove($resetPasswordRequest);
        $this->getEntityManager()->flush();
    }
}
