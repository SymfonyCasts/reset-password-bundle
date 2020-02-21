<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\tests\UnitTests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
class ResetPasswordControllerTraitTest extends TestCase
{
    private const EMAIL_KEY = 'ResetPasswordCheckEmail';
    private const TOKEN_KEY = 'ResetPasswordPublicToken';

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SessionInterface
     */
    private $mockSession;

    /**
     * @inheritDoc
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

    public function testSetsEmailInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::EMAIL_KEY)
        ;

        $fixture = $this->getFixture();
        $fixture->setEmail();
    }

    public function testIsAbleToCheckEmailRemovesKeyFromSessionOnTrue(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with(self::EMAIL_KEY)
            ->willReturn(true)
        ;

        $this->mockSession
            ->expects($this->once())
            ->method('remove')
            ->with(self::EMAIL_KEY)
        ;

        $fixture = $this->getFixture();
        $result = $fixture->getEmail();

        self::assertTrue($result);
    }

    public function testIsAbleToCheckEmailReturnsFalseIfKeyNotFoundInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->willReturn(false)
        ;

        $fixture = $this->getFixture();
        $result = $fixture->getEmail($this->mockSession);

        self::assertFalse($result);
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

        return new class ($container)
        {
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
                return $this->isAbleToCheckEmail();
            }

            public function storeToken(string $token): void
            {
                $this->storeTokenInSession($token);
            }

            public function getToken(): string
            {
                return $this->getTokenFromSession();
            }
        };
    }
}
