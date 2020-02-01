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
        /** @TODO bad oops */
        if (!isset($this->token) && empty($this->token)) {
            throw $this->oops();
        }

        return $this->token;
    }

    private function oops(): \Throwable
    {
        /** @TODO Need something better */
        return new \Exception('OOPS');
    }
}
