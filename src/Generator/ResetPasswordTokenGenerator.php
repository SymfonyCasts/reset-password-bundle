<?php

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @internal
 */
final class ResetPasswordTokenGenerator
{
    public function getToken(string $signingKey, \DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \hash_hmac(
            'sha256',
            $this->encodeHashData($expiresAt, $verifier, $userId),
            $signingKey,
            false
        );
    }

    //@todo make me private | fix tests for private
    private function encodeHashData(\DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \json_encode([
            $verifier,
            $userId,
            $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }
}
