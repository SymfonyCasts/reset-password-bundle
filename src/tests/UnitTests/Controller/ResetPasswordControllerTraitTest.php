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
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SessionInterface
     */
    private $mockSession;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Request
     */
    private $mockRequest;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ResetPasswordHelper
     */
    private $mockHelper;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->mockSession = $this->createMock(SessionInterface::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockHelper = $this->createMock(ResetPasswordHelper::class);
    }

    public function testStoresTokenInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with('key', 'token')
        ;

        $this->mockHelper
            ->expects($this->once())
            ->method('getSessionTokenKey')
            ->willReturn('key')
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->storeToken($this->mockRequest, $this->mockHelper, 'token');
    }

    public function testGetsTokenFromSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with('key')
            ->willReturn('')
        ;

        $this->mockHelper
            ->expects($this->once())
            ->method('getSessionTokenKey')
            ->willReturn('key')
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->getToken($this->mockRequest, $this->mockHelper);
    }

    public function testSetsEmailInSession(): void
    {
        $this->mockSession
            ->expects($this->once())
            ->method('set')
            ->with('key', true)
        ;

        $this->mockHelper
            ->expects($this->once())
            ->method('getSessionEmailKey')
            ->willReturn('key')
        ;

        $this->configureMockRequest();
        $fixture = $this->getFixture();
        $fixture->setEmail($this->mockRequest, $this->mockHelper);
    }

    public function testIsAbleToCheckEmailRemovesKeyFromSessionOnTrue(): void
    {
        $this->mockHelper
            ->expects($this->once())
            ->method('getSessionEmailKey')
            ->willReturn('key')
        ;

        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with('key')
            ->willReturn(true)
        ;

        $this->mockSession
            ->expects($this->once())
            ->method('remove')
            ->with('key')
        ;

        $fixture = $this->getFixture();
        $result = $fixture->getEmail($this->mockSession, $this->mockHelper);

        self::assertTrue($result);
    }

    public function testIsAbleToCheckEmailReturnsFalseIfKeyNotFoundInSession(): void
    {
        $this->mockHelper
            ->expects($this->once())
            ->method('getSessionEmailKey')
            ->willReturn('key')
        ;

        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->willReturn(false)
        ;

        $fixture = $this->getFixture();
        $result = $fixture->getEmail($this->mockSession, $this->mockHelper);

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

            public function setEmail(Request $request, ResetPasswordHelper $helper, bool $value = true): void
            {
                $this->setCanCheckEmailInSession($request, $helper, $value);
            }

            public function getEmail(SessionInterface $session, ResetPasswordHelperInterface $helper): bool
            {
                return $this->isAbleToCheckEmail($session, $helper);
            }

            public function storeToken(Request $request, ResetPasswordHelper $helper, string $token): void
            {
                $this->storeTokenInSession($request, $helper, $token);
            }

            public function getToken(Request $request, ResetPasswordHelper $helper): string
            {
                return $this->getTokenFromSession($request, $helper);
            }
        };
    }
}
