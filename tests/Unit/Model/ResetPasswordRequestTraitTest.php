<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
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
        yield ['selector', ['type' => Types::STRING, 'length' => 20]];
        yield ['hashedToken', ['type' => Types::STRING, 'length' => 100]];
        yield ['requestedAt', ['type' => Types::DATETIME_IMMUTABLE]];
        yield ['expiresAt', ['type' => Types::DATETIME_IMMUTABLE]];
    }

    /**
     * @dataProvider propertyDataProvider
     */
    public function testORMAnnotationSetOnProperty(string $propertyName, array $expectedAttributeProperties): void
    {
        $property = new \ReflectionProperty(ResetPasswordRequestTrait::class, $propertyName);
        $attributes = $property->getAttributes(Column::class);

        self::assertCount(1, $attributes);

        foreach ($expectedAttributeProperties as $argumentName => $expectedValue) {
            $attributeArguments = $attributes[0]->getArguments();

            self::assertArrayHasKey($argumentName, $attributeArguments);
            self::assertSame($expectedValue, $attributeArguments[$argumentName]);
        }
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
