<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
trait ResetPasswordRequestTrait
{
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $selector;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $hashedToken;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $requestedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
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
