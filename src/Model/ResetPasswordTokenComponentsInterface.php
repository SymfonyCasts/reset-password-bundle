<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
interface ResetPasswordTokenComponentsInterface
{
    /**
     * @return string Non-hashed random string used to fetch request objects from persistence
     */
    public function getSelector(): string;

    /**
     * @return string The hashed non-public token used to validate reset password requests
     */
    public function getHashedToken(): string;

    /**
     * The public token consists of a concatenated random non-hashed selector string and random non-hashed verifier string.
     */
    public function getPublicToken(): string;
}
