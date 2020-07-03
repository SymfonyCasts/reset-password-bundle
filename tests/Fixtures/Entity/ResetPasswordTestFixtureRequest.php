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
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 * @ORM\Entity(repositoryClass="SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository")
 */
final class ResetPasswordTestFixtureRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="ResetPasswordTestFixtureUser")
     */
    public $user;

    public function __construct(object $user = null, \DateTimeInterface $expiresAt = null, string $selector = null, string $hashedToken = null)
    {
        if (null !== $user) {
            $this->user = $user;
        }

        if (null !== $expiresAt && null !== $selector && null !== $hashedToken) {
            $this->initialize($expiresAt, $selector, $hashedToken);
        } else {
            $this->requestedAt = new \DateTimeImmutable('now');
            $this->expiresAt = new \DateTimeImmutable(\sprintf('+%s seconds', 3600));
            $this->selector = 'selector';
            $this->hashedToken = 'hashedToken';
        }
    }

    public function setExpiredAt(\DateTimeInterface $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function setRequestedAt(\DateTimeInterface $requestedAt): void
    {
        $this->requestedAt = $requestedAt;
    }

    public function setSelector(string $selector): void
    {
        $this->selector = $selector;
    }

    public function getUser(): object
    {
        return $this->user;
    }
}
