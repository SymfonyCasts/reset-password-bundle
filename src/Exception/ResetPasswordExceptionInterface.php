<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

interface ResetPasswordExceptionInterface extends \Throwable
{
    public function getReason(): string;
}
