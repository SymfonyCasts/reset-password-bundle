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
 * @ORM\Entity(repositoryClass="SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordRequestRepositoryTestFixture")
 */
final class ResetPasswordRequestTestFixture implements ResetPasswordRequestInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $selector;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public $expiresAt;

    public function getRequestedAt(): \DateTimeInterface
    {
    }

    public function isExpired(): bool
    {
    }

    public function getExpiresAt(): \DateTimeInterface
    {
    }

    public function getHashedToken(): string
    {
    }

    public function getUser(): object
    {
    }
}
