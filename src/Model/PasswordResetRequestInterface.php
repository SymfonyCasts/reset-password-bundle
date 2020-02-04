<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

interface PasswordResetRequestInterface
{
    public function getRequestedAt(): \DateTimeImmutable;

    public function isExpired(): bool;

    public function getExpiresAt(): \DateTimeImmutable;

    public function getHashedToken(): string;

    public function getUser();
}
