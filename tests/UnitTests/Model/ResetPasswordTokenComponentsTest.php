<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Model;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponents;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
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
