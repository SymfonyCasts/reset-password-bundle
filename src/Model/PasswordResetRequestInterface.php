<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
