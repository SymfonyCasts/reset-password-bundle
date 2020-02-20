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

    /**
     * @var string Non-hashed token verification string
     */
    private $verifier;

    public function __construct(string $signingKey, ResetPasswordRandomGenerator $generator)
    {
        $this->signingKey = $signingKey;
        $this->randomGenerator = $generator;
        $this->verifier = $this->randomGenerator->getRandomAlphaNumStr(self::RANDOM_STR_LENGTH);
    }

    /**
     * Get a cryptographically secure token with it's non-hashed components.
     *
     * @param mixed  $userId   Unique user identifier
     * @param string $verifier Only required for token comparison
     */
    public function getToken(\DateTimeInterface $expiresAt, $userId, string $verifier = null): ResetPasswordTokenComponents
    {
        if (empty($verifier)) {
            $verifier = $this->verifier;
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
        return \hash_hmac('sha256', $data, $this->signingKey, false);
    }
}
