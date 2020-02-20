<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface ResetPasswordHelperInterface
{
    public function generateResetToken(object $user): ResetPasswordToken;

    public function validateTokenAndFetchUser(string $fullToken): object;

    public function removeResetRequest(string $fullToken): void;

    /**
     * Retrieve the key used to store the public token in the session
     */
    public function getSessionTokenKey(): string;
}
