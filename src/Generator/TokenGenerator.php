<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Exception\TokenException;

/**
 * Generate hashed HMAC token
 *
 * Doesn't care about anything else other than making a token
 */
class TokenGenerator
{
    public const HMAC_HASH_ALGO = 'sha256';

    /** @throws \Throwable */
    public function getToken(
        string $signingKey,
        \DateTimeImmutable $expiresAt,
        string $verifier,
        string $userId
    ): string {
        $checkEmpty = [$signingKey, $verifier, $userId];

        foreach ($checkEmpty as $param) {
            $this->isEmpty($param);
        }

        $this->isExpireValid($expiresAt);

        return $this->generateHash($signingKey, $expiresAt, $verifier, $userId);
    }

    /** @throws \Throwable */
    private function isEmpty(string $value): void
    {
        if (empty($value)) {
            throw TokenException::getIsEmpty();
        }
    }

    /** @throws TokenException */
    private function isExpireValid(\DateTimeImmutable $expire): void
    {
        $time = $expire->getTimestamp();

        if ($time <= time()) {
            throw TokenException::getInvalidTokenExpire();
        }
    }

    protected function generateHash(
        string $signingKey,
        \DateTimeImmutable $expiresAt,
        string $verifier,
        string $userId
    ): string {

        return \hash_hmac(
            self::HMAC_HASH_ALGO,
            $this->encodeHashData($expiresAt, $verifier, $userId),
            $signingKey,
            false
        );
    }

    /**
     * Original credit to Laravel's Str::random() method.
     */
    public function getRandomAlphaNumStr(int $length): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = \random_bytes($size);
//            $bytes = $this->getRandomBytes($size);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

//    protected function getRandomBytes(int $size): string
//    {
//        try {
//            return \random_bytes($size);
//        } catch (\Error $exception) {
//            throw TokenException::getBadBytes();
//        }
//    }

    protected function encodeHashData(\DateTimeImmutable $expiresAt, string $verifier, string $userId): string
    {
        return \json_encode([
            $verifier,
            $userId,
            $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }
}
