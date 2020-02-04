<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

class EmptyTokenStringException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'Token value must be a non empty string.';
    }
}
