<?php

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @internal
 * @final
 */
class ResetPasswordTokenGenerator
{
    private const RANDOM_STR_LENGTH = 20;

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
            $verifier = $this->randomGenerator->getRandomAlphaNumStr(self::RANDOM_STR_LENGTH);
        }

        $selector = $this->randomGenerator->getRandomAlphaNumStr(self::RANDOM_STR_LENGTH);

        $encodedData = \json_encode([$verifier, $userId, $expiresAt->getTimestamp()]);

        return new ResetPasswordTokenComponents(
            $selector,
            $verifier,
            $this->getHashedToken($encodedData)
        );
    }

    private function getHashedToken(string $data): string
    {
        return \base64_encode(\hash_hmac('sha256', $data, $this->signingKey, true));
    }
}
