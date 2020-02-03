<?php

namespace SymfonyCasts\Bundle\ResetPassword;

//@todo cleanup use statements
use Symfony\Component\Security\Core\User\UserInterface;
//use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

interface PasswordResetHelperInterface
{
    //@todo return PasswordResetToken or PasswordResetRequestInterface?
    public function generateResetToken(UserInterface $user): PasswordResetToken;

    public function validateTokenAndFetchUser(string $fullToken): object;
}
