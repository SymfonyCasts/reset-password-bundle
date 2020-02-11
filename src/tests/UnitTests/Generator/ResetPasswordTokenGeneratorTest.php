<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class ResetPasswordTokenGeneratorTest extends TestCase
{
    public function testHashDataEncodesToJson(): void
    {
        //@todo refactor or remove
        $this->markTestSkipped('encodeHashData is private.');
        $mockDateTime = $this->createMock(\DateTimeImmutable::class);
        $mockDateTime
            ->expects($this->once())
            ->method('format')
            ->willReturn('2020')
        ;

        $result = $this->fixture->getEncodeHashedDataProtected($mockDateTime, 'verify', '1234');
        self::assertJson($result);
    }

    public function testHashDataEncodesWithProvidedParams(): void
    {
        //@todo refactor or remove
        $this->markTestSkipped('encodeHashData is private.');
        $mockDateTime = $this->createMock(\DateTimeImmutable::class);
        $mockDateTime
            ->method('format')
            ->willReturn('2020')
        ;

        $result = $this->fixture->getEncodeHashedDataProtected($mockDateTime, 'verify', '1234');
        self::assertJsonStringEqualsJsonString(
        '["verify", "1234", "2020"]',
            $result
        );
    }

    public function testReturnsHmacHashedToken(): void
    {
        $mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
        $mockExpiresAt
            ->expects($this->once())
            ->method('format')
            ->with('Y-m-d\TH:i:s')
            ->willReturn('2020')
        ;

        $signingKey = 'unit-test';
        $verifier = 'verify';
        $userId = '1234';

        $generator = new ResetPasswordTokenGenerator($signingKey);
        $result = $generator->getToken($mockExpiresAt, $verifier, $userId);

        $expected = \hash_hmac(
            'sha256',
            \json_encode([$verifier, $userId, '2020']),
            $signingKey
        );

        self::assertSame($expected, $result);
    }
}
