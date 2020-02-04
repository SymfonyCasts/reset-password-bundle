<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

use SymfonyCasts\Bundle\ResetPassword\Exception\EmptyTokenStringException;

class PasswordResetToken
{
    private $token;
    private $expiresAt;

    public function __construct(string $token, \DateTimeImmutable $expiresAt)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Returns the full token the user should use.
     *
     * Internally, this consists of two parts - the selector and
     * the hashed token - but that's an implementation detail
     * of how the token will later be parsed.
     *
     * @throws EmptyTokenStringException
     */
    public function getToken(): string
    {
        $token = trim($this->token);

        if (!empty($token)) {
            return $token;
        }

        throw new EmptyTokenStringException();
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
