<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

class TokenException extends \LogicException
{
    public static function getBadBytes(): self
    {
        return new self('Invalid length expected. Change $size param to valid int.');
    }

    public static function getIsEmpty(): self
    {
        return new self('TokenGenerator::getToken() contains empty string parameter(s).');
    }

    public static function getInvalidTokenExpire(): self
    {
        return new self('Token expire time is in the past.');
    }
}
