<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword;

use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordHelper implements ResetPasswordHelperInterface
{
    /**
     * The first 20 characters of the token are a "selector".
     */
    private const SELECTOR_LENGTH = 20;

    private $tokenGenerator;
    private $resetPasswordCleaner;
    private $repository;

    /**
     * @var int How long a token is valid in seconds
     */
    private $resetRequestLifetime;

    /**
     * @var int Another password reset cannot be made faster than this throttle time in seconds
     */
    private $requestThrottleTime;

    public function __construct(ResetPasswordTokenGenerator $generator, ResetPasswordCleaner $cleaner, ResetPasswordRequestRepositoryInterface $repository, int $resetRequestLifetime, int $requestThrottleTime)
    {
        $this->tokenGenerator = $generator;
        $this->resetPasswordCleaner = $cleaner;
        $this->repository = $repository;
        $this->resetRequestLifetime = $resetRequestLifetime;
        $this->requestThrottleTime = $requestThrottleTime;
    }

    /**
     * {@inheritdoc}
     *
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
     * @throws TooManyPasswordRequestsException
     */
    public function generateResetToken(object $user, ?int $resetRequestLifetime = null): ResetPasswordToken
    {
        $this->resetPasswordCleaner->handleGarbageCollection();

        if ($availableAt = $this->hasUserHitThrottling($user)) {
            throw new TooManyPasswordRequestsException($availableAt);
        }

        $resetRequestLifetime = $resetRequestLifetime ?: $this->resetRequestLifetime;

        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', $resetRequestLifetime));

        $generatedAt = ($expiresAt->getTimestamp() - $resetRequestLifetime);

        $tokenComponents = $this->tokenGenerator->createToken($expiresAt, $this->repository->getUserIdentifier($user));

        $passwordResetRequest = $this->repository->createResetPasswordRequest(
            $user,
            $expiresAt,
            $tokenComponents->getSelector(),
            $tokenComponents->getHashedToken()
        );

        $this->repository->persistResetPasswordRequest($passwordResetRequest);

        // final "public" token is the selector + non-hashed verifier token
        return new ResetPasswordToken(
            $tokenComponents->getPublicToken(),
            $expiresAt,
            $generatedAt
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExpiredResetPasswordTokenException
     * @throws InvalidResetPasswordTokenException
     */
    public function validateTokenAndFetchUser(string $fullToken): object
    {
        $this->resetPasswordCleaner->handleGarbageCollection();

        if (40 !== \strlen($fullToken)) {
            throw new InvalidResetPasswordTokenException();
        }

        $resetRequest = $this->findResetPasswordRequest($fullToken);

        if (null === $resetRequest) {
            throw new InvalidResetPasswordTokenException();
        }

        if ($resetRequest->isExpired()) {
            throw new ExpiredResetPasswordTokenException();
        }

        $user = $resetRequest->getUser();

        $hashedVerifierToken = $this->tokenGenerator->createToken(
            $resetRequest->getExpiresAt(),
            $this->repository->getUserIdentifier($user),
            substr($fullToken, self::SELECTOR_LENGTH)
        );

        if (false === hash_equals($resetRequest->getHashedToken(), $hashedVerifierToken->getHashedToken())) {
            throw new InvalidResetPasswordTokenException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidResetPasswordTokenException
     */
    public function removeResetRequest(string $fullToken): void
    {
        $request = $this->findResetPasswordRequest($fullToken);

        if (null === $request) {
            throw new InvalidResetPasswordTokenException();
        }

        $this->repository->removeResetPasswordRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenLifetime(): int
    {
        return $this->resetRequestLifetime;
    }

    /**
     * Generate a fake reset token.
     *
     * Use this to generate a fake token so that you can, for example, show a
     * "reset confirmation email sent" page that includes a valid "expiration date",
     * even if the email was not actually found (and so, a true ResetPasswordToken
     * was not actually created).
     *
     * This method should not be used when timing attacks are a concern.
     */
    public function generateFakeResetToken(?int $resetRequestLifetime = null): ResetPasswordToken
    {
        $resetRequestLifetime = $resetRequestLifetime ?: $this->resetRequestLifetime;
        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', $resetRequestLifetime));

        $generatedAt = ($expiresAt->getTimestamp() - $resetRequestLifetime);

        return new ResetPasswordToken('fake-token', $expiresAt, $generatedAt);
    }

    private function findResetPasswordRequest(string $token): ?ResetPasswordRequestInterface
    {
        $selector = substr($token, 0, self::SELECTOR_LENGTH);

        return $this->repository->findResetPasswordRequest($selector);
    }

    private function hasUserHitThrottling(object $user): ?\DateTimeInterface
    {
        /** @var \DateTime|\DateTimeImmutable|null $lastRequestDate */
        $lastRequestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        if (null === $lastRequestDate) {
            return null;
        }

        $availableAt = (clone $lastRequestDate)->add(new \DateInterval("PT{$this->requestThrottleTime}S"));

        if ($availableAt > new \DateTime('now')) {
            return $availableAt;
        }

        return null;
    }
}
