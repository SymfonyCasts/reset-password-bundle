<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;

class PasswordResetHelper
{
    /**
     * The first 20 characters of the token are a "selector"
     */
    private const SELECTOR_LENGTH = 20;

    private $repository;

    private $tokenSigningKey;

    /**
     * @var int How long a token is valid in seconds
     */
    private $resetRequestLifetime;

    /**
     * @var int Another password reset cannot be made faster than this throttle time.
     */
    private $requestThrottleTime;

    private $tokenGenerator;

    public function __construct(PasswordResetRequestRepositoryInterface $repository, string $tokenSigningKey, int $resetRequestLifetime, int $requestThrottleTime, TokenGenerator $generator)
    {
        $this->repository = $repository;
        $this->tokenSigningKey = $tokenSigningKey;
        $this->resetRequestLifetime = $resetRequestLifetime;
        $this->requestThrottleTime = $requestThrottleTime;
        $this->tokenGenerator = $generator;
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

        $expiresAt = (new \DateTimeImmutable('now'))
            ->modify(sprintf('+%d seconds', $this->resetRequestLifetime));

//        $selector = $this->generateRandomAlphaNumericString(self::SELECTOR_LENGTH);
        $selector = $this->tokenGenerator->getRandomAlphaNumStr(self::SELECTOR_LENGTH);

//        $plainVerifierToken = $this->generateRandomAlphaNumericString(20);
        $plainVerifierToken = $this->tokenGenerator->getRandomAlphaNumStr(20);

//        $hashedToken = $this->hashVerifierToken(
//            $plainVerifierToken,
//            $user->getId(),
//            $expiresAt
//        );
        $hashedToken = $this->tokenGenerator->getToken(
            $this->tokenSigningKey,
            $expiresAt,
            $plainVerifierToken,
            $user->getId()
        );

        $passwordResetRequest = $this->repository->createPasswordResetRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );

        $this->repository->persistPasswordResetRequest($passwordResetRequest);

        return new PasswordResetToken(
            // final "public" token is the selector + non-hashed verifier token
            $selector.$plainVerifierToken,
            $expiresAt
        );
    }

    public function validateTokenAndFetchUser(string $fullToken): object
    {
        $resetToken = $this->findToken($fullToken);

        if ($resetToken->isExpired()) {
            throw new ExpiredResetPasswordTokenException();
        }

        $verifierToken = substr($fullToken, self::SELECTOR_LENGTH);

//        $hashedVerifierToken = $this->hashVerifierToken(
//            $verifierToken,
//            $this->repository->getUserIdentifier($resetToken->getUser()),
//            $resetToken->getExpiresAt()
//        );
        $hashedVerifierToken = $this->tokenGenerator->getToken(
            $this->tokenSigningKey,
            $resetToken->getExpiresAt(),
            $verifierToken,
            $this->repository->getUserIdentifier($resetToken->getUser())
        );

        if (false === hash_equals($resetToken->getToken(), $hashedVerifierToken)) {
            throw new InvalidResetPasswordTokenException();
        }

        return $resetToken->getUser();
    }

    private function findToken(string $token): PasswordResetRequestInterface
    {
        $selector = substr($token, 0, self::SELECTOR_LENGTH);

        return $this->repository->findPasswordResetRequest($selector);
    }

//    /**
//     * Original credit to Laravel's Str::random() method.
//     */
//    private function generateRandomAlphaNumericString(int $length)
//    {
//        $string = '';
//
//        while (($len = strlen($string)) < $length) {
//            $size = $length - $len;
//
//            $bytes = random_bytes($size);
//
//            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
//        }
//
//        return $string;
//    }

//    private function hashVerifierToken(string $verifierToken, $userId, \DateTimeInterface $expiresAt): string
//    {
//        return \hash_hmac(
//            'sha256',
//            \json_encode([
//                $verifierToken,
//                $userId,
//                $expiresAt->format('Y-m-d\TH:i:s')
//            ]),
//            $this->tokenSigningKey
//        );
//    }

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
}
