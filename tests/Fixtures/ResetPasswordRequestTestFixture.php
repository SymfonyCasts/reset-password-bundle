<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPasswordTests\Fixtures;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

class ResetPasswordRequestTestFixture implements ResetPasswordRequestInterface
{

    public function getRequestedAt(): \DateTimeInterface
    {
    }

    public function isExpired(): bool
    {
    }

    public function getExpiresAt(): \DateTimeInterface
    {
    }

    public function getHashedToken(): string
    {
    }

    public function getUser()
    {
    }
}
