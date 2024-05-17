<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordRuntimeException;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordTokenGenerator implements ResetPasswordTokenGeneratorInterface
{
    /**
     * @param string $signingKey Unique, random, cryptographically secure string
     */
    public function __construct(
        #[\SensitiveParameter]
        private string $signingKey,
        private ResetPasswordRandomGenerator $generator
    ) {
    }

    /**
     * Get a cryptographically secure token with it's non-hashed components.
     *
     * @param int|string $userId   Unique user identifier
     * @param ?string    $verifier Only required for token comparison
     *
     * @throws ResetPasswordRuntimeException
     */
    public function createToken(\DateTimeInterface $expiresAt, int|string $userId, ?string $verifier = null): ResetPasswordTokenComponents
    {
        if (null === $verifier) {
            $verifier = $this->generator->getRandomAlphaNumStr();
        }

        $selector = $this->generator->getRandomAlphaNumStr();

        try {
            $encodedData = json_encode(value: [$verifier, $userId, $expiresAt->getTimestamp()], flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ResetPasswordRuntimeException(message: 'Unable to create token. Invalid JSON.', previous: $exception);
        }

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
