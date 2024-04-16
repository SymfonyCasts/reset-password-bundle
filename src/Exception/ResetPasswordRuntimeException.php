<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
final class ResetPasswordRuntimeException extends \RuntimeException implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return $this->getMessage();
    }
}
