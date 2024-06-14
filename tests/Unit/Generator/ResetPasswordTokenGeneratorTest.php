<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordTokenGeneratorTest extends TestCase
{
    private MockObject&\DateTimeImmutable $mockExpiresAt;
    private ResetPasswordTokenGenerator $tokenGenerator;

    protected function setUp(): void
    {
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
        $this->tokenGenerator = new ResetPasswordTokenGenerator('secret-key', new ResetPasswordRandomGenerator());
    }

    public function testCreateTokenReturnsValidHashedTokenComponents(): void
    {
        $result = $this->tokenGenerator->createToken($this->mockExpiresAt, 'userId');

        // The public token = "selector token" + "verifier token"
        self::assertSame(20, \strlen($result->getSelector()));
        self::assertSame(40, \strlen($result->getPublicToken()));

        $verifier = substr($result->getPublicToken(), 20, 20);

        $expectedHash = base64_encode(hash_hmac(
            algo: 'sha256',
            data: json_encode([$verifier, 'userId', $this->mockExpiresAt->getTimestamp()]),
            key: 'secret-key',
            binary: true
        ));

        self::assertSame($expectedHash, $result->getHashedToken());
    }

    public function testCreateTokenUsesProvidedVerifierToken(): void
    {
        $result = $this->tokenGenerator->createToken($this->mockExpiresAt, 'userId', '1234');

        $expectedHash = base64_encode(hash_hmac(
            algo: 'sha256',
            data: json_encode(['1234', 'userId', $this->mockExpiresAt->getTimestamp()]),
            key: 'secret-key',
            binary: true
        ));

        self::assertSame($expectedHash, $result->getHashedToken());
    }

    public function testCreateTokenUsesProvidedParams(): void
    {
        $result = $this->tokenGenerator->createToken($this->mockExpiresAt, 'userId', '1234');

        $expectedHash = base64_encode(hash_hmac(
            algo: 'sha256',
            data: json_encode(['1234', 'userId', '0123456789']),
            key: 'secret-key',
            binary: true
        ));

        // We used a "fake" timestamp in our expectedHash
        self::assertNotSame($expectedHash, $result->getHashedToken());

        $expectedHash = base64_encode(hash_hmac(
            algo: 'sha256',
            data: json_encode(['1234', 'bad-user-id', $this->mockExpiresAt->getTimestamp()]),
            key: 'secret-key',
            binary: true
        ));

        // We used a "fake" user id in our expectedHash
        self::assertNotSame($expectedHash, $result->getHashedToken());
    }
}
