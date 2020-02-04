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

use SymfonyCasts\Bundle\ResetPassword\Exception\EmptyTokenStringException;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class PasswordResetToken
{
    private $token;
    private $expiresAt;

    public function __construct(string $token, \DateTimeImmutable $expiresAt)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Returns the full token the user should use.
     *
     * Internally, this consists of two parts - the selector and
     * the hashed token - but that's an implementation detail
     * of how the token will later be parsed.
     *
     * @throws EmptyTokenStringException
     */
    public function getToken(): string
    {
        $token = trim($this->token);

        if (!empty($token)) {
            return $token;
        }

        throw new EmptyTokenStringException();
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
