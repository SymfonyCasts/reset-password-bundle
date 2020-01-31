<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

class PasswordResetToken
{
    private $token;
    private $expiresAt;

    public function __construct(string $token, \DateTimeInterface $expiresAt)
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
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
}
