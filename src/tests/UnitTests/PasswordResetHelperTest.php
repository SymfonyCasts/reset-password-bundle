<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests;

use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\PasswordResetHelper;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\PasswordResetRequestTestFixture;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\UserTestFixture;

class PasswordResetHelperTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|PasswordResetRequestRepositoryInterface  */
    protected $mockRepo;

    /** @var string */
    protected $tokenSigningKey;

    /** @var int */
    protected $resetRequestLifetime;

    /** @var int */
    protected $requestThrottleTime;

    /** @var \PHPUnit\Framework\MockObject\MockObject|TokenGenerator  */
    protected $mockGenerator;

    /** @inheritDoc */
    protected function setUp()
    {
        $this->mockRepo = $this->createMock(PasswordResetRequestRepositoryInterface::class);
        $this->tokenSigningKey = 'unit-test';
        $this->resetRequestLifetime = 99999999;
        $this->requestThrottleTime = 99999999;
        $this->mockGenerator = $this->createMock(TokenGenerator::class);
    }

    protected function getPasswordResetHelper(): PasswordResetHelper
    {
        return new PasswordResetHelper(
            $this->mockRepo,
            $this->tokenSigningKey,
            $this->resetRequestLifetime,
            $this->requestThrottleTime,
            $this->mockGenerator
        );
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['repository'];
        yield ['tokenSigningKey'];
        yield ['resetRequestLifetime'];
        yield ['requestThrottleTime'];
        yield ['tokenGenerator'];
    }

    /**
     * @test
     * @dataProvider propertyDataProvider
     */
    public function hasProperties(string $property): void
    {
        self::assertClassHasAttribute($property, PasswordResetHelper::class);
    }

    /** @test */
    public function hasUserThrottlingReturnsFalseWithNoLastRequestDate(): void
    {
        $user = $this->createMock(UserTestFixture::class);
        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn('1234')
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn(null)
        ;

        $this->mockRepo
            ->method('createPasswordResetRequest')
            ->willReturn(new PasswordResetRequestTestFixture())
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->generateResetToken($user);
    }

    /** @test */
    public function hasUserThrottlingReturnsFalseIfNotBeforeThrottleTime(): void
    {
        $user = $this->createMock(UserTestFixture::class);
        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn('1234')
        ;

        $mockLastRequestTime = $this->createMock(\DateTimeImmutable::class);
        $mockLastRequestTime
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(1234)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn($mockLastRequestTime)
        ;

        $this->mockRepo
            ->method('createPasswordResetRequest')
            ->willReturn(new PasswordResetRequestTestFixture())
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->generateResetToken($user);
    }

    /** @test */
    public function exceptionThrownIfRequestBeforeThrottleLimit(): void
    {
        $user = $this->createMock(UserTestFixture::class);

        $mockLastRequestTime = $this->createMock(\DateTimeImmutable::class);
        $mockLastRequestTime
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(9999999999)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn($mockLastRequestTime)
        ;

        $this->expectException(TooManyPasswordRequestsException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->generateResetToken($user);
    }
}
