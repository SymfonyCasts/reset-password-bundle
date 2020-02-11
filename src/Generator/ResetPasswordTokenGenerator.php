<?php

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @internal
 * @final
 */
class ResetPasswordTokenGenerator
{
    /**
     * @var string Unique, random, cryptographically secure string
     */
    private $signingKey;

    public function __construct(string $signingKey)
    {
        $this->signingKey = $signingKey;
    }

    public function getToken(\DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \hash_hmac(
            'sha256',
            $this->encodeHashData($expiresAt, $verifier, $userId),
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
