<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 * @ORM\Entity(repositoryClass="SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestUuidRepository")
 */
final class ResetPasswordTestFixtureUuidRequest implements ResetPasswordRequestInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
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

    public function __construct()
    {
        $this->id = Uuid::v4();
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
    }

    public function getHashedToken(): string
    {
    }

    public function getUser(): object
    {
        return $this->user;
    }
}
