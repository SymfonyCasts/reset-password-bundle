<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface ResetPasswordRequestRepositoryInterface
{
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface;

    public function getUserIdentifier(object $user): string;

    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface;

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    public function removeExpiredResetPasswordRequests(): int;
}
