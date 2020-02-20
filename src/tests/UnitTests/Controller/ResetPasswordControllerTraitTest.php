<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\tests\UnitTests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;

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

            public function store(Request $request, ResetPasswordHelper $helper, string $token): void
            {
                $this->storeTokenInSession($request, $helper, $token);
            }

            public function get(Request $request, ResetPasswordHelper $helper): string
            {
                return $this->getTokenFromSession($request, $helper);
            }
        };
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

        $fixture = $this->getFixture();
        $fixture->store($this->mockRequest, $this->mockHelper, 'token');
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

        $fixture = $this->getFixture();
        $fixture->get($this->mockRequest, $this->mockHelper);
    }
}
