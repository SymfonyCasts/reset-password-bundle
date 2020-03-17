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
    public function testLengthIs20(): void
    {
        $generator = new ResetPasswordRandomGenerator();
        $result = $generator->getRandomAlphaNumStr();

        self::assertSame(20, \strlen($result));
    }

    public function testIsRandom(): void
    {
        $generator = new ResetPasswordRandomGenerator();

        $resultA = $generator->getRandomAlphaNumStr();
        $resultB = $generator->getRandomAlphaNumStr();

        self::assertNotSame($resultA, $resultB);
    }
}
