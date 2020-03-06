<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class TooManyPasswordRequestsException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'You have already requested a reset password email. Please check your email or try again soon.';
    }
}
