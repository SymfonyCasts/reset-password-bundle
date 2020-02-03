<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests;

use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\PasswordResetHelper;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;

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
}
