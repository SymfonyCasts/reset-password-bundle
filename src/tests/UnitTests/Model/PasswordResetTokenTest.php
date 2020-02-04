<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Exception\EmptyTokenStringException;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenTest extends TestCase
{
    /**
     * @var \DateTimeImmutable|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mockExpiresAt;

    protected function setUp()
    {
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['token'];
        yield ['expiresAt'];
    }

    /**
     * @test
     * @dataProvider propertyDataProvider
     */
    public function hasProperty(string $property): void
    {
        self::assertClassHasAttribute($property, PasswordResetToken::class);
    }

    /** @test */
    public function constructorInitializesProperties(): void
    {
        $expectedToken = '12345';
        $expectedExpires = $this->createMock(\DateTimeImmutable::class);

        $resetToken = new PasswordResetToken($expectedToken, $expectedExpires);

        self::assertSame($expectedToken, $resetToken->getToken());
        self::assertSame($expectedExpires, $resetToken->getExpiresAt());
    }

    public function throwsExceptionWithEmptyToken(): void
    {
        $resetToken = new PasswordResetToken('', $this->mockExpiresAt);

        $this->expectException(EmptyTokenStringException::class);
        $resetToken->getToken();
    }

    public function trimsWhiteSpaceFromToken(): void
    {
        $resetToken = new PasswordResetToken(' test ', $this->mockExpiresAt);
        self::assertSame('test', $resetToken->getToken());
    }
}
