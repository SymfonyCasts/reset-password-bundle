<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Generator;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordRandomGeneratorTest extends TestCase
{
    public function testIsProvidedLength(): void
    {
        $generator = new ResetPasswordRandomGenerator();
        $result = $generator->getRandomAlphaNumStr(100);

        self::assertSame(100, \strlen($result));
    }

    public function testIsRandom(): void
    {
        $generator = new ResetPasswordRandomGenerator();

        $resultA = $generator->getRandomAlphaNumStr(20);
        $resultB = $generator->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }
}
