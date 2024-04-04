<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\FakeRepositoryException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordExceptionTest extends TestCase
{
    public function exceptionDataProvider(): \Generator
    {
        yield [
            new ExpiredResetPasswordTokenException(),
            'The link in your email is expired. Please try to reset your password again.',
        ];
        yield [
            new InvalidResetPasswordTokenException(),
            'The reset password link is invalid. Please try to reset your password again.',
        ];
        yield [
            new TooManyPasswordRequestsException(new \DateTime('+1 hour')),
            'You have already requested a reset password email. Please check your email or try again soon.',
        ];
        yield [
            new FakeRepositoryException(),
            'Please update the request_password_repository configuration in config/packages/reset_password.yaml to point to your "request password repository" service.',
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testIsReason(ResetPasswordExceptionInterface $exception, string $message): void
    {
        self::assertSame($message, $exception->getReason());
    }

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testImplementsResetPasswordExceptionInterface(ResetPasswordExceptionInterface $exception): void
    {
        self::assertInstanceOf(ResetPasswordExceptionInterface::class, $exception);
    }
}
