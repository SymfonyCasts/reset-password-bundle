<?php

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
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

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = \random_bytes($size);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
