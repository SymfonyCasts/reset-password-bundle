<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Model;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordTokenTest extends TestCase
{
    public function testTokenValueIsSafeForStorage(): void
    {
        $token = new ResetPasswordToken('1234', new \DateTimeImmutable(), \time());

        $token->clearToken();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The token property is not set. Calling getToken() after calling clearToken() is not allowed.');

        self::assertSame('void', $token->getToken());
    }

    /**
     * @dataProvider translationIntervalDataProvider
     */
    public function testTranslations(int $lifetime, int $expectedInterval, string $unitOfMeasure): void
    {
        $created = \time();

        $expire = \DateTimeImmutable::createFromFormat('U', (string) ($created + $lifetime));

        $token = new ResetPasswordToken('token', $expire, $created);

        self::assertSame(
            \sprintf('%%count%% %s|%%count%% %ss', $unitOfMeasure, $unitOfMeasure),
            $token->getExpirationMessageKey()
        );

        self::assertSame(['%count%' => $expectedInterval], $token->getExpirationMessageData());
    }

    public function translationIntervalDataProvider(): \Generator
    {
        yield [60, 1, 'minute'];
        yield [900, 15, 'minute'];
        yield [3600, 1, 'hour'];
        yield [7200, 2, 'hour'];
        yield [43200, 12, 'hour'];
        yield [86400, 1, 'day'];
        yield [864000, 10, 'day'];
        yield [2678400, 1, 'month'];
        yield [5356800, 2, 'month'];
        yield [34819200, 1, 'year'];
    }
}
