<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author  Jesse Rushlow <jr@geeshoe.com>
 */
class PasswordResetTokenTest extends AbstractModelUnitTest
{
    protected $sut = ResetPasswordToken::class;

    /**
     * @var \DateTimeImmutable|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mockExpiresAt;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['token', 'private', ''];
        yield ['expiresAt', 'private', ''];
    }

    public function methodDataProvider(): \Generator
    {
        yield ['getToken', 'public'];
        yield ['getExpiresAt', 'public'];
    }

    /**
     * @test
     */
    public function constructorInitializesProperties(): void
    {
        $expectedToken = '12345';
        $expectedExpires = $this->createMock(\DateTimeImmutable::class);

        $resetToken = new ResetPasswordToken($expectedToken, $expectedExpires);

        self::assertSame($expectedToken, $resetToken->getToken());
        self::assertSame($expectedExpires, $resetToken->getExpiresAt());
    }
}
