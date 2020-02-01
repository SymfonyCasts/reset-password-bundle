<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

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
        $resultA = $this->fixture->getRandomAlphaNumStr(20);
        $resultB = $this->fixture->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }

    /** @test */
    public function randomStrReturnsCorrectLength(): void
    {
        $result = $this->fixture->getRandomAlphaNumStr(100);

        self::assertSame(100, strlen($result));
    }

    /** @test */
    public function RandomBytesThrowsExceptionWithBadSize(): void
    {
        $this->expectException(\Error::class);
        $this->fixture->getRandomBytesFromProtected(0);
    }

    /** @test */
    public function getRandomBytesUsesLength(): void
    {
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
        $this->expectException(\Exception::class);

        $mockDate = $this->createMock(\DateTimeImmutable::class);

        $generator = new TokenGenerator();
        $generator->getToken($key, $mockDate, $verifier, $userId);
    }

    /** @test */
    public function returnsHmacHashedToken(): void
    {
        $mockExpectedAt = $this->createMock(\DateTimeImmutable::class);
        $mockExpectedAt
            ->method('format')
            ->willReturn('2020')
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
