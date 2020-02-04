<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Exception;

use SymfonyCasts\Bundle\ResetPassword\Exception\EmptyTokenStringException;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

class EmptyTokenStringExceptionTest extends TestCase
{
    /** @test */
    public function implementsResetPasswordExceptionInterface(): void
    {
        $interfaces = class_implements(EmptyTokenStringException::class);
        self::assertArrayHasKey(ResetPasswordExceptionInterface::class, $interfaces);
    }

    /** @test */
    public function isReason(): void
    {
        $expected = 'Token value must be a non empty string.';
        $exception = new EmptyTokenStringException();

        self::assertSame($expected, $exception->getReason());
    }
}
