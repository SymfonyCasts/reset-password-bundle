<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
class ResetPasswordTokenGeneratorTest extends TestCase
{
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
    protected function setUp(): void
    {
        $this->mockRandomGenerator = $this->createMock(ResetPasswordRandomGenerator::class);
        $this->mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    public function testSelectorGeneratedByRandomGenerator(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method('getRandomAlphaNumStr')
            ->with(20)
        ;

        $generator = $this->getTokenGenerator();
        $generator->createToken($this->mockExpiresAt, 'userId');
    }

    public function testHashedTokenIsCreatedWithExpectedParams(): void
    {
        $this->mockRandomGenerator
            ->expects($this->exactly(2))
            ->method('getRandomAlphaNumStr')
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
        $result = $generator->createToken($this->mockExpiresAt, 'user1234');

        self::assertSame($expected, $result->getHashedToken());
    }

    public function testHashedTokenIsCreatedUsingOptionVerifierParam(): void
    {
        $date = '2020';
        $userId = 'user1234';
        $knownVerifier = 'verified';

        $this->mockRandomGenerator
            ->expects($this->exactly(1))
            ->method('getRandomAlphaNumStr')
            ->willReturnOnConsecutiveCalls('un-used-verifier', 'selector')
        ;

        $this->mockExpiresAt
            ->expects($this->once())
            ->method('format')
            ->willReturn($date)
        ;

        $knownToken = \hash_hmac(
            'sha256',
            \json_encode([$knownVerifier, $userId, $date]),
            'key'
        );

        $generator = $this->getTokenGenerator();
        $result = $generator->createToken($this->mockExpiresAt, $userId, $knownVerifier);

        self::assertSame($knownToken, $result->getHashedToken());
    }

    private function getTokenGenerator(): ResetPasswordTokenGenerator
    {
        return new ResetPasswordTokenGenerator(
            'key',
            $this->mockRandomGenerator
        );
    }
}
