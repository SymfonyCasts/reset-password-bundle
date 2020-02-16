<?php

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
interface ResetPasswordRequestRepositoryInterface
{
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface;

    /**
     * Get the unique user identifier from persistence.
     */
    public function getUserIdentifier(object $user): string;

    /**
     * Save a reset password request entity to persistence.
     */
    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    /**
     * Get a reset password request entity from persistence using the request's selector.
     */
    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface;

    /**
     * Get a users most recent reset password request that is not expired.
     */
    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    /**
     * Remove a single password reset request from persistence.
     */
    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    /**
     * Remove all expired reset password request objects from persistence
     *
     * @return int Number of request objects removed from persistence
     */
    public function removeExpiredResetPasswordRequests(): int;
}
