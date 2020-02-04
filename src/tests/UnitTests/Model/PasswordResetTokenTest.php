<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenTest extends TestCase
{
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
}
