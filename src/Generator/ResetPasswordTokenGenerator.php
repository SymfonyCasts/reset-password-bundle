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
    private $expiresAt;
    private $userId;
    private $verifier;

    public function __construct(string $signingKey, ResetPasswordRandomGenerator $generator, \DateTimeInterface $expiresAt, string $userID)
    {
        $this->signingKey = $signingKey;
        $this->randomGenerator = $generator;
        $this->expiresAt = $expiresAt;
        $this->userId = $userID;
        $this->verifier = $this->randomGenerator->getRandomAlphaNumStr(self::RANDOM_STR_LENGTH);
    }

    public function getToken(): ResetPasswordTokenComponents
    {
        $selector = $this->randomGenerator->getRandomAlphaNumStr(self::RANDOM_STR_LENGTH);

        return new ResetPasswordTokenComponents(
            $selector,
            $this->verifier,
            $this->getHashedToken()
        );
    }

    private function getHashedToken(): string
    {
        return \hash_hmac(
            'sha256',
            $this->encodeHashData($this->expiresAt, $this->verifier, $this->userId),
            $this->signingKey,
            false
        );
    }

    private function encodeHashData(\DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \json_encode([
            $verifier,
            $userId,
            $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }
}
