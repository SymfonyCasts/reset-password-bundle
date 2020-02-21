<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\tests\UnitTests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

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
     * @var \PHPUnit\Framework\MockObject\MockObject|Request
     */
    private $mockRequest;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->mockSession = $this->createMock(SessionInterface::class);
        $this->mockRequest = $this->createMock(Request::class);
    }

    public function testStoresTokenInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::TOKEN_KEY, 'token')
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->storeToken($this->mockRequest, 'token');
    }

    public function testGetsTokenFromSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with(self::TOKEN_KEY)
            ->willReturn('')
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->getToken($this->mockRequest);
    }

    public function testSetsEmailInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with(self::EMAIL_KEY, true)
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->setEmail($this->mockRequest);
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
        $result = $fixture->getEmail($this->mockSession);

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

    private function configureMockRequest(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->mockSession)
        ;
    }

    private function getFixture(): object
    {
        return new class
        {
            use ResetPasswordControllerTrait;

            public function setEmail(Request $request, bool $value = true): void
            {
                $this->setCanCheckEmailInSession($request, $value);
            }

            public function getEmail(SessionInterface $session): bool
            {
                return $this->isAbleToCheckEmail($session);
            }

            public function storeToken(Request $request, string $token): void
            {
                $this->storeTokenInSession($request, $token);
            }

            public function getToken(Request $request): string
            {
                return $this->getTokenFromSession($request);
            }
        };
    }
}
