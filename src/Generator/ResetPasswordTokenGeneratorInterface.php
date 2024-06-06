<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordTokenComponentsInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
interface ResetPasswordTokenGeneratorInterface
{
    public function createToken(\DateTimeInterface $expiresAt, int|string $userId, ?string $verifier = null): ResetPasswordTokenComponentsInterface;
}
