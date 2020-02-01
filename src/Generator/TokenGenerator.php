<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Generator;

use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

class TokenGenerator
{
    //@TODO default algo.. provide option to allow different algo?
    public const HASH_ALGO = 'sha256';

    /** @var PasswordResetToken */
    private $token;

    public function getToken(): PasswordResetToken
    {
        /** @TODO bad oops */
        if (!isset($this->token) && empty($this->token)) {
            throw $this->oops();
        }

        return $this->token;
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

        //@TODO edge cases: empty strings...

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
    protected function randomAlphaNumStr(int $length): string
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
