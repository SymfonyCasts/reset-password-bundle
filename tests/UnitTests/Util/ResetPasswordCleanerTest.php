<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Util;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordCleanerTest extends TestCase
{
    public function testRemoveExpiredRequestCallsRepositoryTrait(): void
    {
        $mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
        $mockRepo
            ->expects($this->once())
            ->method('removeExpiredResetPasswordRequests')
            ->willReturn(1)
        ;

        $cleaner = new ResetPasswordCleaner($mockRepo);
        $result = $cleaner->handleGarbageCollection();

        self::assertSame(1, $result);
    }

    public function testHandleGarbageCollectionCanBeForced(): void
    {
        $mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
        $mockRepo
            ->expects($this->once())
            ->method('removeExpiredResetPasswordRequests')
            ->willReturn(1)
        ;

        $cleaner = new ResetPasswordCleaner($mockRepo, false);
        $cleaner->handleGarbageCollection(true);
    }

    public function testAreNotRemovedWhenDisabledAndNotForced(): void
    {
        $mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
        $mockRepo
            ->expects($this->never())
            ->method('removeExpiredResetPasswordRequests')
        ;

        $cleaner = new ResetPasswordCleaner($mockRepo, false);
        $result = $cleaner->handleGarbageCollection();

        self::assertSame(0, $result);
    }
}
