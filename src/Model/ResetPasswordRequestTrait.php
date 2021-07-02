<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
trait ResetPasswordRequestTrait
{
    /**
     * @ORM\Column(type="string", length=20)
     */
    #[ORM\Column(type: Types::STRING, length: 20)]
    private $selector;

    /**
     * @ORM\Column(type="string", length=100)
     */
    #[ORM\Column(type: Types::STRING, length: 100)]
    private $hashedToken;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private $requestedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private $expiresAt;

    private function initialize(\DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->requestedAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }

    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= time();
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}
