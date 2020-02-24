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
     * Generate & persist a new password reset token that can be provided to the user
     *
     * ResetPasswordHelper persists a ResetPasswordRequest object which contains
     * the hashed token. The ResetPasswordToken object returned by this method
     * contains the user token which is used to select and verify a persisted
     * request object.
     */
    public function generateResetToken(object $user): ResetPasswordToken;

    /**
     * Validate a reset request and fetch the user from persistence
     *
     * The token provided to the user from generateResetToken() is validated
     * against a ResetPasswordRequest object stored in persistence.
     *
     * @param string $fullToken selector string + verifier string provided by the user
     */
    public function validateTokenAndFetchUser(string $fullToken): object;

    /**
     * Remove a single ResetPasswordRequest object from persistence
     *
     * Intended to be used after validation. Not appropriate for clearing
     * expired ResetPasswordRequest objects. Use the built in garbage collector
     * instead.
     *
     * @param string $fullToken selector string + verifier string provided by the user
     */
    public function removeResetRequest(string $fullToken): void;
}
