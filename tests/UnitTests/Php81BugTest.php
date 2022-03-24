<?php

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;
use SymfonyCasts\Bundle\ResetPassword\Tests\FunctionalTests\MockObject;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

class Php81BugTest extends TestCase
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
        $this->mockRepo = $this->createMock(
            ResetPasswordRequestRepositoryInterface::class
        );
        $this->mockTokenGenerator = $this->createMock(
            ResetPasswordTokenGenerator::class
        );
        $this->mockCleaner = $this->createMock(ResetPasswordCleaner::class);
        $this->mockResetRequest = $this->createMock(
            ResetPasswordRequestInterface::class
        );
        $this->randomToken = bin2hex(random_bytes(20));
    }

    public function testWithSystemTimeZone(): void
    {
        $this->runTokenTest();
    }

    public function testWithExplicitTimeZone(): void
    {
        date_default_timezone_set('America/Los_Angeles');

        $this->runTokenTest();
    }

    private function runTokenTest(): void
    {
        $helper = $this->getPasswordResetHelper();

        $token = $helper->generateResetToken(new ResetPasswordTestFixtureUser());

        $result = $token->getExpiresAtIntervalInstance();

        $expected = [
            'y' => 0,
            'm' => 0,
            'd' => 0,
            'h' => 1,
            'i' => 0,
            's' => 0,
        ];

        foreach ($expected as $intervalProperty => $expectedValue) {
            self::assertSame($expectedValue, $result->$intervalProperty);
        }

        self::assertSame(["%count%" => 1], $token->getExpirationMessageData());
        self::assertSame('%count% hour|%count% hours', $token->getExpirationMessageKey());
    }

    private function getPasswordResetHelper(): ResetPasswordHelper
    {
        return new ResetPasswordHelper(
            $this->mockTokenGenerator,
            $this->mockCleaner,
            $this->mockRepo,
            3600,
            3600
        );
    }
}
