<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Exception;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;

/**
 * @author  Jesse Rushlow <jr@geeshoe.com>
 */
class ExceptionTest extends TestCase
{
    public function exceptionDataProvider(): \Generator
    {
        yield [
            ExpiredResetPasswordTokenException::class,
            'The link in your email is expired. Please try to reset your password again.'
        ];
        yield [
            InvalidResetPasswordTokenException::class,
            'The reset password link is invalid. Please try to reset your password again.'
        ];
        yield [
            TooManyPasswordRequestsException::class,
            'You have already requested a reset password email. Please check your email or try again soon.'
        ];
    }

    /**
     * @test
     * @dataProvider exceptionDataProvider
     */
    public function isReason(string $exception, string $message): void
    {
        $result = new $exception();
        self::assertSame($message, $result->getReason());
    }

    /**
     * @test
     * @dataProvider exceptionDataProvider
     */
    public function implementsResetPasswordExceptionInterface(string $exception): void
    {
        $interfaces = class_implements($exception);
        self::assertArrayHasKey(ResetPasswordExceptionInterface::class, $interfaces);
    }
}
