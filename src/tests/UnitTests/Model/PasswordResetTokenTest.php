<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Exception\EmptyTokenStringException;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

/**
 * @author  Jesse Rushlow <jr@geeshoe.com>
 */
class PasswordResetTokenTest extends AbstractModelUnitTest
{
    protected $sut = PasswordResetToken::class;

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
     * @throws EmptyTokenStringException
     */
    public function constructorInitializesProperties(): void
    {
        $expectedToken = '12345';
        $expectedExpires = $this->createMock(\DateTimeImmutable::class);

        $resetToken = new PasswordResetToken($expectedToken, $expectedExpires);

        self::assertSame($expectedToken, $resetToken->getToken());
        self::assertSame($expectedExpires, $resetToken->getExpiresAt());
    }

    /**
     * @test
     * @throws EmptyTokenStringException
     */
    public function throwsExceptionWithEmptyToken(): void
    {
        $resetToken = new PasswordResetToken('', $this->mockExpiresAt);

        $this->expectException(EmptyTokenStringException::class);
        $resetToken->getToken();
    }

    /**
     * @test
     * @throws EmptyTokenStringException
     */
    public function trimsWhiteSpaceFromToken(): void
    {
        $resetToken = new PasswordResetToken(' test ', $this->mockExpiresAt);
        self::assertSame('test', $resetToken->getToken());
    }
}
