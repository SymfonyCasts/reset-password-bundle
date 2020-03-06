<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 * @final
 */
class ResetPasswordTokenComponents
{
    /**
     * @var string non-hashed random string used to fetch request from persistence
     */
    private $selector;

    /**
     * @var string non-hashed string used to verify token
     */
    private $verifier;

    /**
     * @var string
     */
    private $hashedToken;

    public function __construct(string $selector, string $verifier, string $hashedToken)
    {
        $this->selector = $selector;
        $this->verifier = $verifier;
        $this->hashedToken = $hashedToken;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    public function getPublicToken(): string
    {
        return $this->selector.$this->verifier;
    }
}
