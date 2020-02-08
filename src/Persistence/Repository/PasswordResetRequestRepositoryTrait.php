<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
trait PasswordResetRequestRepositoryTrait
{
    public function getUserIdentifier(object $user): string
    {
        return $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user)
        ;
    }

    public function persistResetPasswordRequest(PasswordResetRequestInterface $resetPasswordRequest)
    {
        $this->getEntityManager()->persist($resetPasswordRequest);
        $this->getEntityManager()->flush($resetPasswordRequest);
    }

    public function findResetPasswordRequest(string $selector): ?PasswordResetRequestInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        // Normally there is only 1 max request per use, but written to be flexible
        /** @var PasswordResetRequestInterface $resetPasswordRequest */
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

    public function removeResetPasswordRequest(PasswordResetRequestInterface $resetPasswordRequest): void
    {
        $this->getEntityManager()->remove($resetPasswordRequest);
        $this->getEntityManager()->flush();
    }
}
