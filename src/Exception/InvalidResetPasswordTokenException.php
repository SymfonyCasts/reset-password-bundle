<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

class InvalidResetPasswordTokenException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'The reset password link is invalid. Please try to reset your password again.';
    }
}