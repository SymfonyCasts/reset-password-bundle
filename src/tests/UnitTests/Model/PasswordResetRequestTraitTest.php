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

use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\PasswordResetRequestTraitTestFixture;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 */
class PasswordResetRequestTraitTest extends AbstractModelUnitTest
{
    protected $sut = PasswordResetRequestTraitTestFixture::class;

    /**
     * @var \DateTimeImmutable
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var string
     */
    protected $hashedToken;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->expiresAt = $this->createMock(\DateTimeImmutable::class);
        $this->selector = 'selector';
        $this->hashedToken = 'hashed';
    }

    protected function getFixture(): PasswordResetRequestTraitTestFixture
    {
        return new $this->sut(
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['selector', 'protected', '@ORM\Column(type="string", length=100)'];
        yield ['hashedToken', 'protected', '@ORM\Column(type="string", length=100)'];
        yield ['requestedAt', 'protected', '@ORM\Column(type="datetime_immutable")'];
        yield ['expiresAt', 'protected', '@ORM\Column(type="datetime_immutable")'];
    }

    public function methodDataProvider(): \Generator
    {
        yield ['getRequestedAt', 'public'];
        yield ['isExpired', 'public'];
        yield ['getExpiresAt', 'public'];
        yield ['getHashedToken', 'public'];
    }

    /**
     * @test
     */
    public function getRequestAtReturnsImmutableDateTime(): void
    {
        $trait = $this->getFixture();

        self::assertInstanceOf(\DateTimeImmutable::class, $trait->getRequestedAt());
    }

    /**
     * @test
     */
    public function isExpiredReturnsFalseWithTimeInFuture(): void
    {
        $this->expiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(time() + (360))
        ;

        $trait = $this->getFixture();
        self::assertFalse($trait->isExpired());
    }

    /**
     * @test
     */
    public function isExpiredReturnsTrueWithTimeInPast(): void
    {
        $this->expiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(time() - (360))
        ;

        $trait = $this->getFixture();
        self::assertTrue($trait->isExpired());
    }

    /**
     * @test
     */
    public function getExpiresAtReturnsDateTimeInterface(): void
    {
        $trait = $this->getFixture();

        self::assertInstanceOf(\DateTimeInterface::class, $trait->getExpiresAt());
    }

    /**
     * @test
     */
    public function getHashedTokenReturnsToken(): void
    {
        $trait = $this->getFixture();
        self::assertSame($this->hashedToken, $trait->getHashedToken());
    }
}
