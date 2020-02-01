<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

/**
 * Generate hashed HMAC token
 *
 * Doesn't care about anything else other than making a token
 */
class TokenGenerator
{
    //@TODO default algo.. provide option to allow different algo?
    public const HASH_ALGO = 'sha256';

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

        //@todo edgecase $expiresAt already in the past or invalid value. Save that case for controller/helper?

        return $this->generateHash($signingKey, $expiresAt, $verifier, $userId);
    }

    /** @throws \Throwable */
    private function isEmpty(string $value): void
    {
        if (empty($value)) {
            throw $this->oops();
        }
    }

    private function oops(): \Throwable
    {
        /** @TODO Need something better */
        return new \Exception('OOPS');
    }

    protected function generateHash(
        string $signingKey,
        \DateTimeImmutable $expiresAt,
        string $verifier,
        string $userId
    ): string {

        return \hash_hmac(
            self::HASH_ALGO,
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

            /** @TODO ?Keep separated due to \Throwable? vs handle \Throwable in loop.. */
//            $bytes = random_bytes($size);
            $bytes = $this->getRandomBytes($size);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    protected function getRandomBytes(int $size): string
    {
        //@todo edge case: $size = 0 -> \Error
        /** @TODO Bad oops */
        try {
            return \random_bytes($size);
        } catch (\Exception $exception) {
            throw $this->oops();
        }
    }

    protected function encodeHashData(\DateTimeImmutable $expiresAt, string $verifier, string $userId): string
    {
        return \json_encode([
            $verifier,
            $userId,
            $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }
}
