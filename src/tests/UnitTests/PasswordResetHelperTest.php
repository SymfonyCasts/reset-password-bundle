<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests;

use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\PasswordResetHelper;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\PasswordResetRequestTestFixture;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\UserTestFixture;
use SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model\AbstractModelUnitTest;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 */
class PasswordResetHelperTest extends AbstractModelUnitTest
{
    protected $sut = PasswordResetHelper::class;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PasswordResetRequestRepositoryInterface
     */
    protected $mockRepo;

    /**
     * @var string
     */
    protected $tokenSigningKey;

    /**
     * @var int
     */
    protected $resetRequestLifetime;

    /**
     * @var int
     */
    protected $requestThrottleTime;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TokenGenerator
     */
    protected $mockGenerator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PasswordResetRequestInterface
     */
    protected $mockResetRequest;

    /**
     * @var string
     */
    protected $randomToken;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|UserTestFixture
     */
    protected $mockUserFixture;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->mockRepo = $this->createMock(PasswordResetRequestRepositoryInterface::class);
        $this->tokenSigningKey = 'unit-test';
        $this->resetRequestLifetime = 99999999;
        $this->requestThrottleTime = 99999999;
        $this->mockGenerator = $this->createMock(TokenGenerator::class);
        $this->mockResetRequest = $this->createMock(PasswordResetRequestInterface::class);
        $this->randomToken = \bin2hex(\random_bytes(10));
        $this->mockUserFixture = $this->createMock(UserTestFixture::class);
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
        yield ['repository', 'private', ''];
        yield ['tokenSigningKey', 'private', ''];
        yield ['resetRequestLifetime', 'private', ''];
        yield ['requestThrottleTime', 'private', ''];
        yield ['tokenGenerator', 'private', ''];
    }

    public function methodDataProvider(): \Generator
    {
        yield ['generateResetToken', 'public'];
        yield ['validateTokenAndFetchUser', 'public'];
        yield ['removeResetRequest', 'public'];
        yield ['findToken', 'private'];
        yield ['hasUserHisThrottling', 'private'];

    }

    /**
     * @test
     */
    public function hasUserThrottlingReturnsFalseWithNoLastRequestDate(): void
    {
        $this->mockUserFixture
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
        $helper->generateResetToken($this->mockUserFixture);
    }

    /**
     * @test
     */
    public function hasUserThrottlingReturnsFalseIfNotBeforeThrottleTime(): void
    {
        $this->mockUserFixture
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
        $helper->generateResetToken($this->mockUserFixture);
    }

    /**
     * @test
     */
    public function exceptionThrownIfRequestBeforeThrottleLimit(): void
    {
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
        $helper->generateResetToken($this->mockUserFixture);
    }

    /**
     * @test
     */
    public function removeResetRequestThrowsExceptionWithEmptyToken(): void
    {
        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest('');
    }

    /**
     * @test
     */
    public function removeResetRequestRetrievesTokenFromRepository(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findPasswordResetRequest')
            ->with($this->randomToken)
            ->willReturn($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest($this->randomToken);
    }

    /**
     * @test
     */
    public function removeResetRequestCallsRepositoryToRemoveResetRequestObject(): void
    {
        $this->mockRepo
            ->method('findPasswordResetRequest')
            ->willReturn($this->mockResetRequest)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('removeResetRequest')
            ->with($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->removeResetRequest('1234');
    }

    /**
     * @test
     */
    public function validateTokenThrowsExceptionOnExpiredResetRequest(): void
    {
        $this->mockResetRequest
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true)
        ;

        $this->mockRepo
            ->expects($this->once())
            ->method('findPasswordResetRequest')
            ->with($this->randomToken)
            ->willReturn($this->mockResetRequest)
        ;

        $this->expectException(ExpiredResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    /**
     * @test
     */
    public function validateTokenFetchesUserIfTokenNotExpired(): void
    {
        $this->mockUserFixture
            ->expects($this->once())
            ->method('getId')
            ->willReturn('1234')
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->mockUserFixture)
        ;

        $this->mockRepo
            ->method('findPasswordResetRequest')
            ->with($this->randomToken)
            ->willReturn($this->mockResetRequest)
        ;

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }

    /**
     * @test
     */
    public function validateTokenThrowsExceptionIfTokenAndVerifierDoNotMatch(): void
    {
        $this->mockUserFixture
            ->method('getId')
            ->willReturn('1234')
        ;

        $this->mockResetRequest
            ->method('getUser')
            ->willReturn($this->mockUserFixture)
        ;

        $this->mockResetRequest
            ->expects($this->once())
            ->method('getHashedToken')
            ->willReturn('1234')
        ;

        $this->mockRepo
            ->method('findPasswordResetRequest')
            ->willReturn($this->mockResetRequest)
        ;

        $this->expectException(InvalidResetPasswordTokenException::class);

        $helper = $this->getPasswordResetHelper();
        $helper->validateTokenAndFetchUser($this->randomToken);
    }
}
