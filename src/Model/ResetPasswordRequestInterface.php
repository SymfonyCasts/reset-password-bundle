<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
interface ResetPasswordRequestInterface
{
    /**
     * Get the time the reset password request was created.
     */
    public function getRequestedAt(): \DateTimeInterface;

    /**
     * Check if the reset password request is expired.
     */
    public function isExpired(): bool;

    /**
     * Get the time the reset password request expires.
     */
    public function getExpiresAt(): \DateTimeInterface;

    /**
     * Get the non-public hashed token used to verify a request password request.
     */
    public function getHashedToken(): string;

    /**
     * Get the user whom requested a password reset.
     */
    public function getUser(): object;
}
