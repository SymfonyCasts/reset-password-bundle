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

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
trait PasswordResetRequestTrait
{
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $selector;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $hashedToken;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $requestedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $expiresAt;

    public function __construct(\DateTimeImmutable $expiresAt, string $selector, string $hashedToken)
    {
        $this->requestedAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }

    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= time();
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}
