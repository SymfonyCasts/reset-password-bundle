<?php

namespace SymfonyCasts\Bundle\ResetPassword\Util;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 *
 * @internal
 * @final
 */
class ResetPasswordCleaner
{
    /**
     * @var bool Enable/disable garbage collection
     */
    private $enabled;

    private $repository;

    public function __construct(ResetPasswordRequestRepositoryInterface $repository, bool $enabled = true)
    {
        $this->repository = $repository;
        $this->enabled = $enabled;
    }

    /**
     * Clears expired reset password requests from persistence.
     *
     * Enable/disable in configuration. Calling with $force = true
     * will attempt to remove expired requests regardless of
     * configuration setting.
     */
    public function handleGarbageCollection(bool $force = false): int
    {
        if ($this->enabled || (!$this->enabled && $force)) {
            return $this->removeExpiredRequests();
        }

        return 0;
    }

    private function removeExpiredRequests(): int
    {
        return $this->repository->removeExpiredResetPasswordRequests();
    }
}
