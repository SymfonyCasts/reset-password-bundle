<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGeneratorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleanerInterface;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 * @author  Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordHelperTest extends TestCase
{
    private MockObject&ResetPasswordRequestRepositoryInterface $mockRepo;
    private MockObject&ResetPasswordTokenGeneratorInterface $tokenGenerator;
    private MockObject&ResetPasswordRequestInterface $mockResetRequest;
    private MockObject&ResetPasswordCleanerInterface $mockCleaner;
    private string $randomToken;
    private int $requestLifetime = 99999999;
    private int $requestThrottleTime = 99999999;

    protected function setUp(): void
    {
        $this->mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
        $this->tokenGenerator = $this->createMock(ResetPasswordTokenGeneratorInterface::class);
        $this->mockCleaner = $this->createMock(ResetPasswordCleanerInterface::class);
        $this->mockResetRequest = $this->createMock(ResetPasswordRequestInterface::class);
        $this->randomToken = bin2hex(random_bytes(20));
        $this->requestLifetime = 99999999;
        $this->requestThrottleTime = 99999999;
    }

    public function testGenerateResetTokenCallsGarbageCollector(): void
    {
        $this->mockCleaner
            ->expects($this->once())
            ->method('handleGarbageCollection')
        ;

        // We don't care about the mock configuration below, we're only testing if garbage collection is called.
        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->getPasswordResetHelper()->generateResetToken(new \stdClass());
    }

    public function testHasUserThrottlingReturnsNullWithNoLastRequestDate(): void
    {
        $user = new \stdClass();

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->with($user)
            ->willReturn(null)
        ;

        // We don't care about the mock configuration below, we're only testing the helpers hasUserItThrottling method.
        $this->mockRepo
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1234')
        ;

        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('createResetPasswordRequest')
            ->willReturn(new ResetPasswordTestFixtureRequest())
        ;

        $this->getPasswordResetHelper()->generateResetToken(new \stdClass());
    }

    public function testHasUserThrottlingReturnsNullIfNotBeforeThrottleTime(): void
    {
        $user = new \stdClass();

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->with($user)
            ->willReturn(new \DateTime('-3 hours'))
        ;

        // We don't care about the mock configuration below, we're only testing the helpers hasUserItThrottling method.
        $this->mockRepo
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1234')
        ;

        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('createResetPasswordRequest')
            ->willReturn(new ResetPasswordTestFixtureRequest())
        ;

        $this->requestThrottleTime = 7200; // 2 hours
        $this->getPasswordResetHelper()->generateResetToken(new \stdClass());
    }

    public function testExceptionThrownIfRequestBeforeThrottleLimit(): void
    {
        $user = new \stdClass();

        $this->mockRepo
            ->expects($this->once())
            ->method('getMostRecentNonExpiredRequestDate')
            ->with($user)
            ->willReturn(new \DateTime('-1 hour'))
        ;

        $this->requestThrottleTime = 7200; // 2 hours

        try {
            $this->getPasswordResetHelper()->generateResetToken($user);
        } catch (TooManyPasswordRequestsException $exception) {
            // account for time changes during test
            self::assertGreaterThanOrEqual(3599, $exception->getRetryAfter());
            self::assertLessThanOrEqual(3600, $exception->getRetryAfter());

            return;
        }

        $this->fail('Exception was not thrown.');
    }

    public function testExpiresAtUsesCurrentTimeZone(): void
    {
        // We don't care about the mock configuration below, we're only testing if the correct timezone is used.
        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $token = $this->getPasswordResetHelper()->generateResetToken(new \stdClass());

        $expiresAt = $token->getExpiresAt();
        self::assertSame(date_default_timezone_get(), $expiresAt->getTimezone()->getName());
    }

    public function testExpiresAtUsingDefaultLifetime(): void
    {
        // We don't care about the mock configuration below, we're only testing .
        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->requestLifetime = 60;

        $token = $this->getPasswordResetHelper()->generateResetToken(new \stdClass());
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+55 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+65 seconds'), $expiresAt);
    }

    public function testExpiresAtUsingOverrideLifetime(): void
    {
        // We don't care about the mock configuration below, we're only testing .
        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->requestLifetime = 60;

        $token = $this->getPasswordResetHelper()->generateResetToken(new \stdClass(), 30);
        $expiresAt = $token->getExpiresAt();

        self::assertGreaterThan(new \DateTimeImmutable('+25 seconds'), $expiresAt);
        self::assertLessThan(new \DateTimeImmutable('+35 seconds'), $expiresAt);
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
        $user = new \stdClass();
        $expiresAt = new \DateTimeImmutable();

        $this->mockResetRequest
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(false)
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getExpiresAt')
            ->willReturn($expiresAt)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('findResetPasswordRequest')
            ->with(substr($this->randomToken, 0, 20))
            ->willReturn($this->mockResetRequest)
        ;

        $this->mockRepo
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->with($user)
            ->willReturn('1234')
        ;

        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->with($expiresAt, '1234', substr($this->randomToken, 20, 20))
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    public function testValidateTokenThrowsExceptionIfTokenAndVerifierDoNotMatch(): void
    {
        $user = new \stdClass();
        $expiresAt = new \DateTimeImmutable();

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getExpiresAt')
            ->willReturn($expiresAt)
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
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

        $this->mockRepo
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->with($user)
            ->willReturn('1234')
        ;

        $this->tokenGenerator
            ->expects(self::once())
            ->method('createToken')
            ->with($expiresAt, '1234', substr($this->randomToken, 20, 20))
            ->willReturn(new ResetPasswordTokenComponents('', '', ''))
        ;

        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
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

    public function testFakeTokenExpiresAtUsingDefault(): void
    {
        $helper = new ResetPasswordHelper(
            $this->tokenGenerator,
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
            $this->tokenGenerator,
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
            $this->tokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            $this->requestLifetime,
            $this->requestThrottleTime
        );
    }
}
