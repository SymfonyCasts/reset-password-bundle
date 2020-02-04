<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

interface PasswordResetRequestRepositoryInterface
{
    public function createPasswordResetRequest(UserInterface $user, \DateTimeImmutable $expiresAt, string $selector, string $hashedToken): PasswordResetRequestInterface;

    public function persistPasswordResetRequest(PasswordResetRequestInterface $passwordResetRequest);

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequestInterface;

    public function getMostRecentNonExpiredRequestDate(UserInterface $user): ?\DateTimeImmutable;
}
