<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class PasswordResetHelper implements PasswordResetHelperInterface
{
    /**
     * Length of time a reset request is valid for
     */
    public const LIFETIME_HOURS = 1;

    /**
     * The first 20 characters of the token are a "selector"
     */
    private const SELECTOR_LENGTH = 20;

    /**
     * @var PasswordResetRequestRepositoryInterface
     */
    private $repository;

    /**
     * @var string Unique, random, cryptographically secure string
     */
    private $tokenSigningKey;

    /**
     * @var int How long a token is valid in seconds
     */
    private $resetRequestLifetime;

    /**
     * @var int Another password reset cannot be made faster than this throttle time.
     */
    private $requestThrottleTime;

    /**
     * @var ResetPasswordTokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var ResetPasswordRandomGenerator
     */
    private $randomGenerator;

    public function __construct(PasswordResetRequestRepositoryInterface $repository, string $tokenSigningKey, int $resetRequestLifetime, int $requestThrottleTime, ResetPasswordTokenGenerator $generator, ResetPasswordRandomGenerator $randomGenerator)
    {
        $this->repository = $repository;
        $this->tokenSigningKey = $tokenSigningKey;
        $this->resetRequestLifetime = $resetRequestLifetime;
        $this->requestThrottleTime = $requestThrottleTime;
        $this->tokenGenerator = $generator;
        $this->randomGenerator = $randomGenerator;
    }

    /**
     * Creates a PasswordResetToken object
     *
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     */
    public function generateResetToken(object $user): PasswordResetToken
    {
        if ($this->hasUserHisThrottling($user)) {
            throw new TooManyPasswordRequestsException();
        }

        $expiresAt = $this->getNewExpiresAt();
        $selector = $this->randomGenerator->getRandomAlphaNumStr(self::SELECTOR_LENGTH);
        $plainVerifierToken = $this->randomGenerator->getRandomAlphaNumStr(self::SELECTOR_LENGTH);

        $hashedToken = $this->tokenGenerator->getToken(
            $this->tokenSigningKey,
            $expiresAt,
            $plainVerifierToken,
            $this->repository->getUserIdentifier($user)
        );

        $passwordResetRequest = $this->repository->createResetPasswordRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );

        $this->repository->persistResetPasswordRequest($passwordResetRequest);

        return new PasswordResetToken(
            // final "public" token is the selector + non-hashed verifier token
            $selector.$plainVerifierToken,
            $expiresAt
        );
    }

    /**
     * Validate a PasswordResetRequest and fetch user from persistence
     *
     * @param string $fullToken selector + non-hashed verifier token
     * @throws ExpiredResetPasswordTokenException
     * @throws InvalidResetPasswordTokenException
     */
    public function validateTokenAndFetchUser(string $fullToken): object
    {
        $resetRequest = $this->findToken($fullToken);

        if ($resetRequest->isExpired()) {
            throw new ExpiredResetPasswordTokenException();
        }

        $user = $resetRequest->getUser();

        $verifierToken = substr($fullToken, self::SELECTOR_LENGTH);

        $hashedVerifierToken = $this->tokenGenerator->getToken(
            $this->tokenSigningKey,
            $resetRequest->getExpiresAt(),
            $verifierToken,
            $this->repository->getUserIdentifier($user)
        );

        if (false === hash_equals($resetRequest->getHashedToken(), $hashedVerifierToken)) {
            throw new InvalidResetPasswordTokenException();
        }

        return $user;
    }

    /**
     * Remove a single PasswordResetRequest object from the database
     *
     * @param string $fullToken selector + non-hashed verifier token
     * @throws InvalidResetPasswordTokenException
     */
    public function removeResetRequest(string $fullToken): void
    {
        if (empty($fullToken)) {
            throw new InvalidResetPasswordTokenException();
        }

        $request = $this->findToken($fullToken);
        $this->repository->removeResetPasswordRequest($request);
    }

    private function findToken(string $token): ResetPasswordRequestInterface
    {
        $selector = substr($token, 0, self::SELECTOR_LENGTH);

        return $this->repository->findResetPasswordRequest($selector);
    }

    private function hasUserHisThrottling(object $user): bool
    {
        $lastRequestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        if (!$lastRequestDate) {
            return false;
        }

        if (($lastRequestDate->getTimestamp() + $this->requestThrottleTime) > time()) {
            return true;
        }

        return false;
    }

    private function getNewExpiresAt(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable('now'))
            ->modify(sprintf('+%d seconds', $this->resetRequestLifetime));
    }
}
