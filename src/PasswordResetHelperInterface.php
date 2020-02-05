<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

interface PasswordResetHelperInterface
{
    public function generateResetToken(UserInterface $user): PasswordResetToken;

    public function validateTokenAndFetchUser(string $fullToken): UserInterface;

    public function removeResetRequest(string $fullToken): void;
}
