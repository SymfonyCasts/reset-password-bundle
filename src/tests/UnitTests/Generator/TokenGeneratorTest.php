<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Exception\TokenException;
use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\TokenGeneratorTestFixture;

class TokenGeneratorTest extends TestCase
{
    /** @var TokenGeneratorTestFixture */
    public $fixture;

    protected function setUp()
    {
        $this->fixture = new TokenGeneratorTestFixture();
    }

    /** @test */
    public function randomStrReturned(): void
    {
        $generator = new TokenGenerator();

        $resultA = $generator->getRandomAlphaNumStr(20);
        $resultB = $generator->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }

    /** @test */
    public function randomStrReturnsCorrectLength(): void
    {
        $generator = new TokenGenerator();
        $result = $generator->getRandomAlphaNumStr(100);

        self::assertSame(100, strlen($result));
    }

    /** @test */
    public function randomBytesThrowsExceptionWithBadSize(): void
    {
        //@todo Remove after refactoring token generator
        $this->markTestSkipped('Not catching random_bytes error. Prob safe to remove test.');
        $this->expectException(TokenException::class);
        $this->fixture->getRandomBytesFromProtected(0);
    }

    /** @test */
    public function getRandomBytesUsesLength(): void
    {
        //@todo method removed, do better
        $this->markTestSkipped('Method removed, make me better..');
        $result = $this->fixture->getRandomBytesFromProtected(100);

        $this->assertSame(200, strlen(bin2hex($result)));
    }

    /** @test */
    public function hashDataEncodesToJson(): void
    {
        $mockDateTime = $this->createMock(\DateTimeImmutable::class);
        $mockDateTime
            ->expects($this->once())
            ->method('format')
            ->willReturn('2020')
        ;

        $result = $this->fixture->getEncodeHashedDataProtected($mockDateTime, 'verify', '1234');
        self::assertJson($result);
    }

    /** @test */
    public function hashDataEncodesWithProvidedParams(): void
    {
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

    /** @test */
    public function returnsHashedHmac(): void
    {
        $key = 'abc';

        $mockTime = $this->createMock(\DateTimeImmutable::class);
        $mockTime
            ->method('format')
            ->willReturn('2020')
        ;

        $verifier = 'verify';
        $userId = '1234';

        $hashed = $this->fixture->getGenerateHashProtected(
            $key,
            $mockTime,
            $verifier,
            $userId
        );

        $expected = \hash_hmac(
            'sha256',
            \json_encode([$verifier, $userId, '2020']),
            $key
        );

        self::assertSame($expected, $hashed);
    }

    public function emptyParamDataProvider(): \Generator
    {
        yield ['', 'verify', 'user'];
        yield ['key', '', 'user'];
        yield ['key', 'verify', ''];
    }

    /**
     * @test
     * @dataProvider emptyParamDataProvider
     */
    public function throwsExceptionWithEmptyParams($key, $verifier, $userId): void
    {
        $this->expectException(TokenException::class);

        $mockDate = $this->createMock(\DateTimeImmutable::class);

        $generator = new TokenGenerator();
        $generator->getToken($key, $mockDate, $verifier, $userId);
    }

    /** @test */
    public function throwsExceptionIfExpiresInThePast(): void
    {
        $mockDate = $this->createMock(\DateTimeImmutable::class);
        $mockDate
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(1580685011)
        ;

        $this->expectException(TokenException::class);

        $generator = new TokenGenerator();
        $generator->getToken('x', $mockDate, 'x', 'x');
    }

    /** @test */
    public function returnsHmacHashedToken(): void
    {
        $mockExpectedAt = $this->createMock(\DateTimeImmutable::class);
        $mockExpectedAt
            ->method('format')
            ->willReturn('2020')
        ;

        $mockExpectedAt
            ->method('getTimestamp')
            ->willReturn(9999999999999)
        ;

        $signingKey = 'abcd';
        $verifier = 'verify';
        $userId = '1234';

        $generator = new TokenGenerator();
        $result = $generator->getToken($signingKey, $mockExpectedAt, $verifier, $userId);

        $expected = \hash_hmac(
            'sha256',
            \json_encode([$verifier, $userId, '2020']),
            $signingKey
        );

        self::assertSame($expected, $result);
    }
}
