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
    public function createPasswordResetRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequestInterface;

    public function getUserIdentifier(object $user): string;

    public function persistPasswordResetRequest(PasswordResetRequestInterface $passwordResetRequest);

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequestInterface;

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    public function removeResetRequest(PasswordResetRequestInterface $resetRequest): void;
}
