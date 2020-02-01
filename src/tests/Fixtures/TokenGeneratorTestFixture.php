<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\Fixtures;

use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;

class TokenGeneratorTestFixture extends TokenGenerator
{
    public function getRandomAlphaNumStr(int $length): string
    {
        return $this->randomAlphaNumStr($length);
    }

    public function getRandomBytesFromProtected(int $size): string
    {
        return $this->getRandomBytes($size);
    }

    public function getEncodeHashedDataProtected(
        \DateTimeImmutable $dateTimeImmutable,
        string $verifier,
        $userId
    ): string {
        return $this->encodeHashData($dateTimeImmutable, $verifier, $userId);
    }

    public function getGenerateHashProtected(
        string $signingKey,
        \DateTimeImmutable $dateTime,
        string $verifier,
        string $userId
    ): string
    {
        return $this->generateHash($signingKey, $dateTime, $verifier, $userId);
    }
}
