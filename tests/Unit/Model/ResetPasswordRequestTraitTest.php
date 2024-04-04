<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordRequestTraitTest extends TestCase
{
    public function testIsCompatibleWithInterface(): void
    {
        self::assertInstanceOf(ResetPasswordRequestInterface::class, $this->getFixture(new \DateTimeImmutable()));
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['selector', '@ORM\Column(type="string", length=20)'];
        yield ['hashedToken', '@ORM\Column(type="string", length=100)'];
        yield ['requestedAt', '@ORM\Column(type="datetime_immutable")'];
        yield ['expiresAt', '@ORM\Column(type="datetime_immutable")'];
    }

    /**
     * @dataProvider propertyDataProvider
     */
    public function testORMAnnotationSetOnProperty(string $propertyName, string $expectedAnnotation): void
    {
        $property = new \ReflectionProperty(ResetPasswordRequestTrait::class, $propertyName);
        $result = $property->getDocComment();

        self::assertStringContainsString($expectedAnnotation, $result, sprintf('%s::%s does not contain "%s" in the docBlock.', ResetPasswordRequestTrait::class, $propertyName, $expectedAnnotation));
    }

    public function isExpiredDataProvider(): \Generator
    {
        yield 'Is expired' => [time() + 360, false];
        yield 'Is Not Expired' => [time() - 360, true];
    }

    /**
     * @dataProvider isExpiredDataProvider
     */
    public function testIsExpiredMethod(int $timestamp, bool $expected): void
    {
        $expiresAt = $this->createMock(\DateTimeImmutable::class);
        $expiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn($timestamp)
        ;

        $trait = $this->getFixture($expiresAt);
        self::assertSame($expected, $trait->isExpired());
    }

    private function getFixture($expiresAt): ResetPasswordRequestInterface
    {
        return new class($expiresAt, '', '') implements ResetPasswordRequestInterface {
            use ResetPasswordRequestTrait;

            public function __construct($expiresAt, $selector, $token)
            {
                $this->initialize($expiresAt, $selector, $token);
            }

            /*
             * getUser() is intentionally left out of the trait.
             */
            public function getUser(): object
            {
            }
        };
    }
}
