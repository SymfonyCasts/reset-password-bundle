<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Generator;

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
    /**
     * @var MockObject|ResetPasswordRandomGenerator
     */
    private $mockRandomGenerator;

    /**
     * @var MockObject|\DateTimeImmutable
     */
    private $mockExpiresAt;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockRandomGenerator = $this->createMock(ResetPasswordRandomGenerator::class);
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    public function testSelectorGeneratedByRandomGenerator(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method('getRandomAlphaNumStr')
        ;

        $generator = $this->getTokenGenerator();
        $generator->createToken($this->mockExpiresAt, 'userId');
    }

    public function testHashedTokenIsCreatedWithExpectedParams(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method('getRandomAlphaNumStr')
            ->willReturnOnConsecutiveCalls('verifier', 'selector')
        ;

        $this->mockExpiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(2020)
        ;

        $expected = hash_hmac(
            'sha256',
            json_encode(['verifier', 'user1234', 2020]),
            'key',
            true
        );

        $generator = $this->getTokenGenerator();
        $result = $generator->createToken($this->mockExpiresAt, 'user1234');

        self::assertSame(base64_encode($expected), $result->getHashedToken());
    }

    public function testHashedTokenIsCreatedUsingOptionVerifierParam(): void
    {
        $date = 2020;
        $userId = 'user1234';
        $knownVerifier = 'verified';

        $this->mockRandomGenerator
            ->expects($this->once())
            ->method('getRandomAlphaNumStr')
            ->willReturnOnConsecutiveCalls('un-used-verifier', 'selector')
        ;

        $this->mockExpiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn($date)
        ;

        $knownToken = hash_hmac(
            'sha256',
            json_encode([$knownVerifier, $userId, $date]),
            'key',
            true
        );

        $generator = $this->getTokenGenerator();
        $result = $generator->createToken($this->mockExpiresAt, $userId, $knownVerifier);

        self::assertSame(base64_encode($knownToken), $result->getHashedToken());
    }

    private function getTokenGenerator(): ResetPasswordTokenGenerator
    {
        return new ResetPasswordTokenGenerator('key', $this->mockRandomGenerator);
    }
}
