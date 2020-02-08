<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface PasswordResetRequestInterface
{
    public function getRequestedAt(): \DateTimeInterface;

    public function isExpired(): bool;

    public function getExpiresAt(): \DateTimeInterface;

    public function getHashedToken(): string;

    public function getUser();
}
