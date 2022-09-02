<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 *
 * @ORM\Entity(repositoryClass="SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository")
 */
final class ResetPasswordTestFixtureRequest implements ResetPasswordRequestInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $selector;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public $expiresAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public $requestedAt;

    /**
     * @ORM\ManyToOne(targetEntity="ResetPasswordTestFixtureUser")
     */
    public $user;

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
    }

    public function getHashedToken(): string
    {
    }

    public function getUser(): object
    {
        return $this->user;
    }
}
