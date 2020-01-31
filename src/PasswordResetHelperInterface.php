<?php

namespace SymfonyCasts\Bundle\ResetPassword;


use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

interface PasswordResetHelperInterface
{
    public function generateResetToken(object $user): PasswordResetRequestInterface;

    public function validateTokenAndFetchUser(string $fullToken): object;
}
