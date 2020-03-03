<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence\Fake;

use SymfonyCasts\Bundle\ResetPassword\Exception\FakeRepositoryException;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

@trigger_error('FakeResetPasswordInternalRepository is only a placeholder. It\'s signature should be replaced in config/packages/reset_password.yaml', E_USER_WARNING);

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class FakeResetPasswordInternalRepository implements ResetPasswordRequestRepositoryInterface
{
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        $this->throwException();
    }

    public function getUserIdentifier(object $user): string
    {
        $this->throwException();
    }

    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->throwException();
    }

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        $this->throwException();
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        $this->throwException();
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->throwException();
    }

    public function removeExpiredResetPasswordRequests(): int
    {
        $this->throwException();
    }

    private function throwException(): void
    {
        throw new FakeRepositoryException();
    }
}
