<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordRuntimeException;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class ResetPasswordToken
{
    private ?string $token;

    /**
     * @var int expiresAt translator interval
     */
    private int $transInterval = 0;

    /**
     * @param string $token       selector + non-hashed verifier token
     * @param int    $generatedAt timestamp when the token was created
     */
    public function __construct(
        string $token,
        private \DateTimeInterface $expiresAt,
        private int $generatedAt,
    ) {
        $this->token = $token;
    }

    /**
     * Returns the full token the user should use.
     *
     * Internally, this consists of two parts - the selector and
     * the hashed token - but that's an implementation detail
     * of how the token will later be parsed.
     *
     * @throws ResetPasswordRuntimeException
     */
    public function getToken(): string
    {
        if (null === $this->token) {
            throw new ResetPasswordRuntimeException('The token property is not set. Calling getToken() after calling clearToken() is not allowed.');
        }

        return $this->token;
    }

    /**
     * Allow the token object to be safely persisted in a session.
     */
    public function clearToken(): void
    {
        $this->token = null;
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Get the translation message for when a token expires.
     *
     * This is used in conjunction with getExpirationMessageData() method.
     * Example usage in a Twig template:
     *
     * <p>{{ components.expirationMessageKey|trans(components.expirationMessageData) }}</p>
     *
     * symfony/translation is required to translate into a non-English locale.
     *
     * @throws ResetPasswordRuntimeException
     */
    public function getExpirationMessageKey(): string
    {
        $interval = $this->getExpiresAtIntervalInstance();

        switch ($interval) {
            case $interval->y > 0:
                $this->transInterval = $interval->y;

                return '%count% year|%count% years';
            case $interval->m > 0:
                $this->transInterval = $interval->m;

                return '%count% month|%count% months';
            case $interval->d > 0:
                $this->transInterval = $interval->d;

                return '%count% day|%count% days';
            case $interval->h > 0:
                $this->transInterval = $interval->h;

                return '%count% hour|%count% hours';
            default:
                $this->transInterval = $interval->i;

                return '%count% minute|%count% minutes';
        }
    }

    /**
     * @return array<string, int>
     *
     * @throws ResetPasswordRuntimeException
     */
    public function getExpirationMessageData(): array
    {
        $this->getExpirationMessageKey();

        return ['%count%' => $this->transInterval];
    }

    /**
     * Get the interval that the token is valid for.
     *
     * @throws ResetPasswordRuntimeException
     */
    public function getExpiresAtIntervalInstance(): \DateInterval
    {
        $createdAtTime = \DateTimeImmutable::createFromFormat('U', (string) $this->generatedAt);

        if (false === $createdAtTime) {
            throw new ResetPasswordRuntimeException(\sprintf('Unable to create DateTimeInterface instance from "generatedAt": %s', $this->generatedAt));
        }

        return $this->expiresAt->diff($createdAtTime);
    }
}
