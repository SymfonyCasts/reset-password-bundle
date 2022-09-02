<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 *
 * @final
 */
class ResetPasswordTokenComponents
{
    /**
     * @var string
     */
    private $selector;

    /**
     * @var string
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

    /**
     * @return string Non-hashed random string used to fetch request objects from persistence
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @return string The hashed non-public token used to validate reset password requests
     */
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    /**
     * The public token consists of a concatenated random non-hashed selector string and random non-hashed verifier string.
     */
    public function getPublicToken(): string
    {
        return $this->selector.$this->verifier;
    }
}
