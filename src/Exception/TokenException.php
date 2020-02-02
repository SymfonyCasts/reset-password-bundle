<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

class TokenException extends \LogicException
{
    public static function getBadBytes(): string
    {
        return 'Invalid length expected. Change $size param to valid int.';
    }

    public static function getIsEmpty(): string
    {
        return 'TokenGenerator::getToken() contains empty string parameter(s).';
    }
}
