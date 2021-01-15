<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordControllerTraitTest extends TestCase
{
    private const EMAIL_KEY = 'ResetPasswordCheckEmail';
    private const TOKEN_KEY = 'ResetPasswordPublicToken';
    private const TOKEN_OBJECT_KEY = 'ResetPasswordToken';

    /**
     * @var MockObject|SessionInterface
     */
    private $mockSession;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockSession = $this->createMock(SessionInterface::class);
    }

    public function testStoresTokenInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::TOKEN_KEY, 'token')
        ;

        $fixture = $this->getFixture();
        $fixture->storeToken('token');
    }

    public function testGetsTokenFromSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with(self::TOKEN_KEY)
            ->willReturn('')
        ;

        $fixture = $this->getFixture();
        $fixture->getToken();
    }

    /**
     * @group legacy
     */
    public function testSetsEmailFlagInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::EMAIL_KEY)
        ;

        $fixture = $this->getFixture();
        $fixture->setEmail();
    }

    /**
     * @group legacy
     */
    public function testCanCheckEmailUsesCorrectKey(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('has')
            ->with(self::EMAIL_KEY)
            ->willReturn(true)
        ;

        $fixture = $this->getFixture();
        $fixture->getEmail();
    }

    public function testSetsResetTokenInSession(): void
    {
        $token = new ResetPasswordToken('1234', new \DateTimeImmutable(), 1234);

        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::TOKEN_OBJECT_KEY, $token)
        ;

        $fixture = $this->getFixture();
        $fixture->storeResetPasswordToken($token);
    }

    public function testGetsResetTokenFromSession(): void
    {
        $token = new ResetPasswordToken('1234', new \DateTimeImmutable(), 1234);

        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with(self::TOKEN_OBJECT_KEY)
            ->willReturn($token)
        ;

        $fixture = $this->getFixture();
        $result = $fixture->getResetPasswordToken();

        self::assertSame($token, $result);
    }

    public function testCleanSessionAfterServiceRemovesByTokenAndEmailKeys(): void
    {
        $this->mockSession
            ->expects($this->exactly(3))
            ->method('remove')
            ->withConsecutive([self::TOKEN_KEY], [self::EMAIL_KEY], [self::TOKEN_OBJECT_KEY])
        ;

        $fixture = $this->getFixture();
        $fixture->clearSession();
    }

    /**
     * @return MockObject|ContainerInterface
     */
    private function getConfiguredMockContainer()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->mockSession)
        ;

        $mockRequestStack = $this->createMock(RequestStack::class);
        $mockRequestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($mockRequest)
        ;

        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer
            ->expects($this->once())
            ->method('get')
            ->with('request_stack')
            ->willReturn($mockRequestStack)
        ;

        return $mockContainer;
    }

    private function getFixture(): object
    {
        $container = $this->getConfiguredMockContainer();

        return new class($container) {
            use ResetPasswordControllerTrait;

            private $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function setEmail(): void
            {
                $this->setCanCheckEmailInSession();
            }

            public function getEmail(): bool
            {
                return $this->canCheckEmail();
            }

            public function storeToken(string $token): void
            {
                $this->storeTokenInSession($token);
            }

            public function getToken(): string
            {
                return $this->getTokenFromSession();
            }

            public function storeResetPasswordToken(ResetPasswordToken $token): void
            {
                $this->setTokenObjectInSession($token);
            }

            public function getResetPasswordToken(): ?ResetPasswordToken
            {
                return $this->getTokenObjectFromSession();
            }

            public function clearSession(): void
            {
                $this->cleanSessionAfterReset();
            }
        };
    }
}
