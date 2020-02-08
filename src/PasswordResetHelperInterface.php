<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface PasswordResetHelperInterface
{
    public function generateResetToken(object $user): ResetPasswordToken;

    public function validateTokenAndFetchUser(string $fullToken): object;

    public function removeResetRequest(string $fullToken): void;
}
