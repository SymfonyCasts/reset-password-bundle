<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class ResetPasswordTokenGeneratorTest extends TestCase
{
    private const RANDOM_STR_LENGTH = 20;
    private const RANDOM_GENERATOR_METHOD_NAME = 'getRandomAlphaNumStr';

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ResetPasswordRandomGenerator
     */
    private $mockRandomGenerator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\DateTimeImmutable
     */
    private $mockExpiresAt;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->mockRandomGenerator = $this->createMock(ResetPasswordRandomGenerator::class);
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    private function getTokenGenerator(): ResetPasswordTokenGenerator
    {
        return new ResetPasswordTokenGenerator(
            'key',
            $this->mockRandomGenerator,
            $this->mockExpiresAt,
            'user1234'
        );
    }

    public function testConstructorGetsVerifierFromRandomGenerator(): void
    {
        $this->mockRandomGenerator
            ->expects($this->once())
            ->method(self::RANDOM_GENERATOR_METHOD_NAME)
            ->with(self::RANDOM_STR_LENGTH)
            ->willReturn('rando-str')
        ;

        $this->getTokenGenerator();
    }

    public function testSelectorGeneratedByRandomGenerator(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method(self::RANDOM_GENERATOR_METHOD_NAME)
            ->with(self::RANDOM_STR_LENGTH)
        ;

        $generator = $this->getTokenGenerator();
        $generator->getToken();
    }

    public function testHashedTokenIsCreatedWithExpectedParams(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method(self::RANDOM_GENERATOR_METHOD_NAME)
            ->willReturnOnConsecutiveCalls('verifier', 'selector')
        ;

        $this->mockExpiresAt
            ->expects($this->once())
            ->method('format')
            ->willReturn('2020')
        ;

        $expected = \hash_hmac(
            'sha256',
            \json_encode(['verifier', 'user1234', '2020']),
            'key'
        );

        $generator = $this->getTokenGenerator();
        $result = $generator->getToken();

        self::assertSame($expected, $result->getHashedToken());
    }
}
