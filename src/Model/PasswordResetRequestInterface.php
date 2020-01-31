<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

interface PasswordResetRequestInterface
{
    public function getRequestedAt(): \DateTimeImmutable;

    public function isExpired(): bool;

    public function getExpiresAt(): \DateTimeInterface;

    public function getHashedToken(): string;

    public function getUser();
}
