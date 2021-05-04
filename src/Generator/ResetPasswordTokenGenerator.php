<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 * @final
 */
class ResetPasswordTokenGenerator
{
    /**
     * @var string Unique, random, cryptographically secure string
     */
    private $signingKey;

    /**
     * @var ResetPasswordRandomGenerator
     */
    private $randomGenerator;

    public function __construct(string $signingKey, ResetPasswordRandomGenerator $generator)
    {
        $this->signingKey = $signingKey;
        $this->randomGenerator = $generator;
    }

    /**
     * Get a cryptographically secure token with it's non-hashed components.
     *
     * @param mixed  $userId   Unique user identifier
     * @param string $verifier Only required for token comparison
     */
    public function createToken(\DateTimeInterface $expiresAt, $userId, string $verifier = null): ResetPasswordTokenComponents
    {
        if (null === $verifier) {
            $verifier = $this->randomGenerator->getRandomAlphaNumStr();
        }

        $selector = $this->randomGenerator->getRandomAlphaNumStr();

        $encodedData = json_encode([$verifier, $userId, $expiresAt->getTimestamp()]);

        return new ResetPasswordTokenComponents(
            $selector,
            $verifier,
            $this->getHashedToken($encodedData)
        );
    }

    private function getHashedToken(string $data): string
    {
        return base64_encode(hash_hmac('sha256', $data, $this->signingKey, true));
    }
}
