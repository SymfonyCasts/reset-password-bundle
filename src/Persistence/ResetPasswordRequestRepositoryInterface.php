<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
interface ResetPasswordRequestRepositoryInterface
{
    /**
     * Create a new ResetPasswordRequest object.
     *
     * @param object $user        User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     * @param string $selector    A non-hashed random string used to fetch a request from persistence
     * @param string $hashedToken The hashed token used to verify a reset request
     */
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface;

    /**
     * Get the unique user entity identifier from persistence.
     *
     * @param object $user User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(object $user): string;

    /**
     * Save a reset password request entity to persistence.
     */
    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    /**
     * Get a reset password request entity from persistence, if one exists, using the request's selector.
     *
     * @param string $selector A non-hashed random string used to fetch a request from persistence
     */
    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface;

    /**
     * Get the most recent non-expired reset password request date for the user, if one exists.
     *
     * @param object $user User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     */
    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    /**
     * Remove this reset password request from persistence and any other for this user.
     *
     * This method should remove this ResetPasswordRequestInterface and also all
     * other ResetPasswordRequestInterface objects in storage for the same user.
     */
    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    /**
     * Remove all expired reset password request objects from persistence.
     *
     * @return int Number of request objects removed from persistence
     */
    public function removeExpiredResetPasswordRequests(): int;
}
