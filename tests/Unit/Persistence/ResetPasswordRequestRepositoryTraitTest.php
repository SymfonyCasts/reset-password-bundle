<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Unit\Persistence;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordRequestRepositoryTraitTest extends TestCase
{
    public function testTraitIsCompatibleWithInterface(): void
    {
        $fixture = new class implements ResetPasswordRequestRepositoryInterface {
            use ResetPasswordRequestRepositoryTrait;

            public function createResetPasswordRequest(
                object $user,
                \DateTimeInterface $expiresAt,
                string $selector,
                string $hashedToken
            ): ResetPasswordRequestInterface {
            }
        };

        self::assertInstanceOf(ResetPasswordRequestRepositoryInterface::class, $fixture);
    }
}
