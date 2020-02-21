<?php

namespace SymfonyCasts\Bundle\ResetPasswordTests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class ResetPasswordRandomGeneratorTest extends TestCase
{
    public function testIsProvidedLength(): void
    {
        $generator = new ResetPasswordRandomGenerator();
        $result = $generator->getRandomAlphaNumStr(100);

        self::assertSame(100, strlen($result));
    }

    public function testIsRandom(): void
    {
        $generator = new ResetPasswordRandomGenerator();

        $resultA = $generator->getRandomAlphaNumStr(20);
        $resultB = $generator->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }
}
