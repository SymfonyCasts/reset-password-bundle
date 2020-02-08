<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface PasswordResetRequestRepositoryInterface
{
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequestInterface;

    public function getUserIdentifier(object $user): string;

    public function persistResetPasswordRequest(PasswordResetRequestInterface $resetPasswordRequest);

    public function findResetPasswordRequest(string $selector): ?PasswordResetRequestInterface;

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    public function removeResetPasswordRequest(PasswordResetRequestInterface $resetPasswordRequest): void;
}
