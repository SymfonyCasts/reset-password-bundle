<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
interface ResetPasswordHelperInterface
{
    /**
     * Generates & persists a new password reset token that can be provided to the user
     */
    public function generateResetToken(object $user): ResetPasswordToken;

    /**
     * Validate a reset request and fetch the user from persistence
     *
     * The token provided to the user from generateResetToken() is validated
     * against a ResetPasswordRequest object stored in persistence.
     *
     * @param string $fullToken selector + verifier token
     */
    public function validateTokenAndFetchUser(string $fullToken): object;

    /**
     * Remove a single ResetPasswordRequest object from persistence
     *
     * Intended to be used after validation. Not appropriate for clearing
     * expired ResetPasswordRequest objects. Use the built in garbage collector
     * instead.
     */
    public function removeResetRequest(string $fullToken): void;
}
