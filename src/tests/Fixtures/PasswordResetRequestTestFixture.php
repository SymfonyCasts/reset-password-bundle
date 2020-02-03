<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\Fixtures;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

class PasswordResetRequestTestFixture implements PasswordResetRequestInterface
{

    public function getRequestedAt(): \DateTimeImmutable
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
