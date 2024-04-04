<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TooManyPasswordRequestsExceptionTest extends TestCase
{
    public function testCanGetRetryAfter(): void
    {
        $exception = new TooManyPasswordRequestsException(new \DateTime('+1 hour'));

        // account for time changes during test
        self::assertGreaterThanOrEqual(3599, $exception->getRetryAfter());
        self::assertLessThanOrEqual(3600, $exception->getRetryAfter());
    }
}
