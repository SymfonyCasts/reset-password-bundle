<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

class ExpiredResetPasswordTokenException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'The link in your email is expired. Please try to reset your password again.';
    }
}
