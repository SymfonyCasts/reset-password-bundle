<?php

namespace SymfonyCasts\Bundle\ResetPasswordTests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordTokenComponentsTest extends TestCase
{
    public function testReturnsSelectAndVerifierAsString(): void
    {
        $tokenComponents = new ResetPasswordTokenComponents(
            'selector',
            'Verifier',
            'xyz'
        );

        $expected = 'selectorVerifier';
        self::assertSame($expected, $tokenComponents->getPublicToken());
    }
}
