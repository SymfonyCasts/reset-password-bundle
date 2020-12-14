<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class ResetPasswordToken
{
    /**
     * @var string selector + non-hashed verifier token
     */
    private $token;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    /**
     * @var int|null timestamp when the token was created
     */
    private $generatedAt;

    /**
     * @var int expiresAt translator interval
     */
    private $transInterval = 0;

    public function __construct(string $token, \DateTimeInterface $expiresAt, int $generatedAt = null)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->generatedAt = $generatedAt;

        if (null === $generatedAt) {
            $this->triggerDeprecation();
        }
    }

    /**
     * Returns the full token the user should use.
     *
     * Internally, this consists of two parts - the selector and
     * the hashed token - but that's an implementation detail
     * of how the token will later be parsed.
     */
    public function getToken(): string
    {
        return $this->token;
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
     * @throws \LogicException
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
     * @throws \LogicException
     */
    public function getExpirationMessageData(): array
    {
        $this->getExpirationMessageKey();

        return ['%count%' => $this->transInterval];
    }

    /**
     * Get the interval that the token is valid for.
     *
     * @throws \LogicException
     *
     * @psalm-suppress PossiblyFalseArgument
     */
    public function getExpiresAtIntervalInstance(): \DateInterval
    {
        if (null === $this->generatedAt) {
            throw new \LogicException(\sprintf('%s initialized without setting the $generatedAt timestamp.', self::class));
        }

        $createdAtTime = \DateTimeImmutable::createFromFormat('U', (string) $this->generatedAt);

        return $this->expiresAt->diff($createdAtTime);
    }

    private function triggerDeprecation(): void
    {
        trigger_deprecation(
            'symfonycasts/reset-password-bundle',
            '1.2',
            'Initializing the %s without setting the "$generatedAt" constructor argument is deprecated. The default "null" will be removed in the future.',
            self::class
        );
    }
}
