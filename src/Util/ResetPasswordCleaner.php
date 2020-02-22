<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Util;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

class ResetPasswordCleaner
{
    private $enabled;

    private $repository;

    public function __construct(ResetPasswordRequestRepositoryInterface $repository, bool $enabled = true)
    {
        $this->repository = $repository;
        $this->enabled = $enabled;
    }

    public function handleGarbageCollection(): int
    {
        if ($this->enabled) {
            return $this->removeExpiredRequests();
        }

        return 0;
    }

    private function removeExpiredRequests(): int
    {
        return $this->repository->removeExpiredResetPasswordRequests();
    }
}
