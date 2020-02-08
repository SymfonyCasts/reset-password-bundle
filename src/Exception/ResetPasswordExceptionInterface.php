<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface ResetPasswordExceptionInterface extends \Throwable
{
    public function getReason(): string;
}
