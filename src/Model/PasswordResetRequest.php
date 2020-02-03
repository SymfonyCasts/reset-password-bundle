<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Model;

class PasswordResetRequest implements PasswordResetRequestInterface
{
    use PasswordResetRequestTrait;

    public function getUser()
    {
        // TODO: Implement getUser() method.
    }
}
