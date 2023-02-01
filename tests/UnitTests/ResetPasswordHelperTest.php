<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 * @author  Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordHelperTest extends TestCase
{
    /**
     * @var MockObject|ResetPasswordRequestRepositoryInterface
     */
    private $mockRepo;

    /**
     * @var MockObject|ResetPasswordTokenGenerator
     */
    private $mockTokenGenerator;

    /**
     * @var MockObject|ResetPasswordRequestInterface
     */
    private $mockResetRequest;

    /**
     * @var MockObject|ResetPasswordCleaner
     */
    private $mockCleaner;

    /**
     * @var string
     */
    private $randomToken;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
        $this->mockTokenGenerator = $this->createMock(ResetPasswordTokenGenerator::class);
        $this->mockCleaner = $this->createMock(ResetPasswordCleaner::class);
        $this->mockResetRequest = $this->createMock(ResetPasswordRequestInterface::class);
        $this->randomToken = bin2hex(random_bytes(20));
    }

    /**
     * @covers \SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper::hasUserHitThrottling
     */
    public function testHasUserThrottlingReturnsFalseWithNoLastRequestDate(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1234')
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn(null)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('createResetPasswordRequest')
            ->willReturn(new ResetPasswordTestFixtureRequest())
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->generateResetToken(new \stdClass());
    }

    /**
     * @covers \SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper::hasUserHitThrottling
     */
    public function testHasUserThrottlingReturnsNullIfNotBeforeThrottleTime(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1234')
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn(new \DateTime('-3 hours'))
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('createResetPasswordRequest')
            ->willReturn(new ResetPasswordTestFixtureRequest())
        ;

        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            99999999,
            7200 // 2 hours
        );

        $helper->generateResetToken(new \stdClass());
    }

    public function testExceptionThrownIfRequestBeforeThrottleLimit(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->willReturn(new \DateTime('-1 hour'))
        ;

        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            99999999,
            7200 // 2 hours
        );

        try {
            $helper->generateResetToken(new \stdClass());
        } catch (TooManyPasswordRequestsException $exception) {
            // account for time changes during test
            self::assertGreaterThanOrEqual(3599, $exception->getRetryAfter());
            self::assertLessThanOrEqual(3600, $exception->getRetryAfter());

            return;
        }

        $this->fail('Exception was not thrown.');
    }

    public function testRemoveResetRequestThrowsExceptionWithEmptyToken(): void
    {
        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest('');
    }

    public function testRemoveResetRequestRetrievesTokenFromRepository(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->with(substr($this->randomToken, 0, 20))
            ->willReturn($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest($this->randomToken);
    }

    public function testRemoveResetRequestCallsRepositoryToRemoveResetRequestObject(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->willReturn($this->mockResetRequest)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('removeResetPasswordRequest')
            ->with($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest('1234');
    }

    public function testExceptionThrownIfTokenLengthIsNotOfCorrectSize(): void
    {
        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser(substr($this->randomToken, 0, 39));
    }

    public function testExceptionIsThrownIfTokenNotFoundDuringValidation(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->willReturn(null)
        ;

        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testValidateTokenThrowsExceptionOnExpiredResetRequest(): void
    {
        $this->mockResetRequest
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->with(substr($this->randomToken, 0, 20))
            ->willReturn($this->mockResetRequest)
        ;

        $this->expectException(ExpiredResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testValidateTokenFetchesUserIfTokenNotExpired(): void
    {
        $this->mockResetRequest
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(false)
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new \stdClass())
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getExpiresAt')
            ->willReturn(new \DateTimeImmutable())
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->with(substr($this->randomToken, 0, 20))
            ->willReturn($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testValidateTokenThrowsExceptionIfTokenAndVerifierDoNotMatch(): void
    {
        $this->mockResetRequest
            ->expects($this->once())
            ->method('getExpiresAt')
            ->willReturn(new \DateTimeImmutable())
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new \stdClass())
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getHashedToken')
            ->willReturn('1234')
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->willReturn($this->mockResetRequest)
        ;

        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testGenerateResetTokenCallsGarbageCollector(): void
    {
        $this->mockCleaner
            ->expects($this->once())
            ->method('handleGarbageCollection')
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->generateResetToken(new \stdClass());
    }

    public function testGarbageCollectorCalledDuringValidation(): void
    {
        $this->mockCleaner
            ->expects($this->once())
            ->method('handleGarbageCollection')
        ;

        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testExpiresAtUsesCurrentTimeZone(): void
    {
        $helper = $this->getPasswordResetHelper();
        $token = $helper->generateResetToken(new \stdClass());

        $expiresAt = $token->getExpiresAt();
        self::assertSame(date_default_timezone_get(), $expiresAt->getTimezone()->getName());
    }

    public function testExpiresAtUsingDefault(): void
    {
        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            60,
            99999999
        );

        $token = $helper->generateResetToken(new \stdClass());
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+55 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+65 seconds'), $expiresAt);
    }

    public function testExpiresAtUsingOverride(): void
    {
        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            60,
            99999999
        );

        $token = $helper->generateResetToken(new \stdClass(), 30);
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+25 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+35 seconds'), $expiresAt);
    }

    public function testFakeTokenExpiresAtUsingDefault(): void
    {
        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            60,
            99999999
        );

        $token = $helper->generateFakeResetToken();
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+55 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+65 seconds'), $expiresAt);
    }

    public function testFakeTokenExpiresAtUsingOverride(): void
    {
        $helper = new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            60,
            99999999
        );

        $token = $helper->generateFakeResetToken(30);
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+25 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+35 seconds'), $expiresAt);
    }

    private function getPasswordResetHelper(): ResetPasswordHelper
    {
        return new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            99999999,
            99999999
        );
    }
}
