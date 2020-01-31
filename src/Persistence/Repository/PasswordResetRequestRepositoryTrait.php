<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequest;

trait PasswordResetRequestRepositoryTrait
{
    public function createPasswordResetRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequest
    {
        return new PasswordResetRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );
    }

    public function getUserIdentifier(object $user): string
    {
        $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user);
    }

    public function persistPasswordResetRequest(PasswordResetRequest $passwordResetRequest)
    {
        $this->getEntityManager()->persist($passwordResetRequest);
        $this->getEntityManager()->flush($passwordResetRequest);
    }

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequest
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        // Normally there is only 1 max request per use, but written to be flexible
        /** @var PasswordResetRequest[] $resetRequests */
        $resetRequests = $this->createQueryBuilder('t')
            ->select('t.requestedAt')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->execute();

        foreach ($resetRequests as $resetRequest) {
            if (!$resetRequest->isExpired()) {
                return $resetRequest->getRequestedAt();
            }
        }

        return null;
    }
}
