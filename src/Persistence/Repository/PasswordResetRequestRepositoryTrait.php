<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

trait PasswordResetRequestRepositoryTrait
{
    //@TODO Maker creates method in app as PasswordResetRequest is a userland entity
//    public function createPasswordResetRequest(UserInterface $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequestInterface
//    {
//        return new PasswordResetRequest(
//            $user,
//            $expiresAt,
//            $selector,
//            $hashedToken
//        );
//    }

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
        /** @var PasswordResetRequestInterface[] $resetRequests */
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
