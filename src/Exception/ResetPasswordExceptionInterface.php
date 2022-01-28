<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
interface ResetPasswordExceptionInterface extends \Throwable
{
    public const MESSAGE_PROBLEM_VALIDATE = 'There was a problem validating your password reset request';
    public const MESSAGE_PROBLEM_HANDLE = 'There was a problem handling your password reset request';

    public function getReason(): string;
}
