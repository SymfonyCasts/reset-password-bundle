<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class ResetPasswordRandomGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function isProvidedLength(): void
    {
        $generator = new ResetPasswordRandomGenerator();
        $result = $generator->getRandomAlphaNumStr(100);

        self::assertSame(100, strlen($result));
    }

    /**
     * @test
     */
    public function isRandom(): void
    {
        $generator = new ResetPasswordRandomGenerator();

        $resultA = $generator->getRandomAlphaNumStr(20);
        $resultB = $generator->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }
}
