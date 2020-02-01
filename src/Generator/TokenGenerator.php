<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

class TokenGenerator
{
    /** @var PasswordResetToken */
    private $token;

    public function getToken(): PasswordResetToken
    {
        if (!isset($this->token) && empty($this->token)) {
            $this->oops();
        }

        return $this->token;
    }

    /** @throws \Exception */
    private function oops(): void
    {
        throw new \Exception('OOPS');
    }
}
