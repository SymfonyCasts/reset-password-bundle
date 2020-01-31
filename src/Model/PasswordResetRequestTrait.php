<?php

namespace SymfonyCasts\Bundle\ResetPassword\Model;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime")
     */
    protected $requestedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expiresAt;

    public function __construct(\DateTimeInterface $expiresAt, string $selector, string $hashedToken)
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

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}
