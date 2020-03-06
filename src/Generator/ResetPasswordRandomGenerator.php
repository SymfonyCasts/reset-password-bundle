<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 * @final
 */
class ResetPasswordRandomGenerator
{
    /**
     * Original credit to Laravel's Str::random() method.
     */
    public function getRandomAlphaNumStr(int $length): string
    {
        $string = '';

        while (($len = \strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = \random_bytes($size);

            $string .= \substr(
                \str_replace(['/', '+', '='], '', \base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
