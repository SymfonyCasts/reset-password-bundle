<?php

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @internal
 */
class TokenGenerator
//final class TokenGenerator @TODO make final? fix tests to do so
{
    //@TODO who was supposed to use me
    public const HMAC_HASH_ALGO = 'sha256';

    public function getToken(string $signingKey, \DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \hash_hmac(
            self::HMAC_HASH_ALGO,
            $this->encodeHashData($expiresAt, $verifier, $userId),
            $signingKey,
            false
        );
    }

    //@todo make me private | fix tests for private
    protected function encodeHashData(\DateTimeInterface $expiresAt, string $verifier, string $userId): string
    {
        return \json_encode([
            $verifier,
            $userId,
            $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }

//    /** @throws \Throwable */
//    private function isEmpty(string $value): void
//    {
//        if (empty($value)) {
//            throw TokenException::getIsEmpty();
//        }
//    }
//
//    /** @throws TokenException */
//    private function isExpireValid(\DateTimeImmutable $expire): void
//    {
//        $time = $expire->getTimestamp();
//
//        if ($time <= time()) {
//            throw TokenException::getInvalidTokenExpire();
//        }
//    }
//
//    protected function generateHash(
//        string $signingKey,
//        \DateTimeImmutable $expiresAt,
//        string $verifier,
//        string $userId
//    ): string {
//
//        return \hash_hmac(
//            self::HMAC_HASH_ALGO,
//            $this->encodeHashData($expiresAt, $verifier, $userId),
//            $signingKey,
//            false
//        );
//    }

//    /**
//     * @TODO get me outta here
//     * Original credit to Laravel's Str::random() method.
//     */
//    public function getRandomAlphaNumStr(int $length): string
//    {
//        $string = '';
//
//        while (($len = strlen($string)) < $length) {
//            $size = $length - $len;
//
//            $bytes = \random_bytes($size);
////            $bytes = $this->getRandomBytes($size);
//
//            $string .= substr(
//                str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
//        }
//
//        return $string;
//    }

//    protected function getRandomBytes(int $size): string
//    {
//        try {
//            return \random_bytes($size);
//        } catch (\Error $exception) {
//            throw TokenException::getBadBytes();
//        }
//    }
}
