<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

interface PasswordResetRequestRepositoryInterface
{
    public function createPasswordResetRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequest;

    public function persistPasswordResetRequest(PasswordResetRequestInterface $passwordResetRequest);

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequestInterface;

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;
}
