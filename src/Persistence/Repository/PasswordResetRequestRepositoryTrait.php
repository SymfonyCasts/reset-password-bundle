<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

trait PasswordResetRequestRepositoryTrait
{
    //@todo implement method or remove it
    public function getUserIdentifier(UserInterface $user): string
    {
        $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user);
    }

    public function persistPasswordResetRequest(PasswordResetRequestInterface $passwordResetRequest)
    {
        $this->getEntityManager()->persist($passwordResetRequest);
        $this->getEntityManager()->flush($passwordResetRequest);
    }

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequestInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(UserInterface $user): ?\DateTimeImmutable
    {
        // Normally there is only 1 max request per use, but written to be flexible
        /** @var PasswordResetRequestInterface $resetRequest */
        $resetRequest = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneorNullResult()
        ;

        if (null !== $resetRequest && !$resetRequest->isExpired()) {
            return $resetRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetRequest(PasswordResetRequestInterface $resetRequest): void
    {
        $this->getEntityManager()->remove($resetRequest);
        $this->getEntityManager()->flush();
    }
}
