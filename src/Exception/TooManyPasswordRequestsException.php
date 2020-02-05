<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class TooManyPasswordRequestsException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'You have already requested a reset password email. Please check your email or try again soon.';
    }
}