<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordRequestTestFixture implements ResetPasswordRequestInterface
{
    public function getRequestedAt(): \DateTimeInterface
    {
    }

    public function isExpired(): bool
    {
    }

    public function getExpiresAt(): \DateTimeInterface
    {
    }

    public function getHashedToken(): string
    {
    }

    public function getUser(): object
    {
    }
}
