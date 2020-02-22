<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Util;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

class ResetPasswordCleaner
{
    private $repository;

    public function __construct(ResetPasswordRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function removeExpiredRequests(): int
    {
        return $this->repository->removeExpiredResetPasswordRequests();
    }
}
