<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Util;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
class ResetPasswordCleanerTest extends TestCase
{
    /**
     * @var MockObject|ResetPasswordRequestRepositoryInterface
     */
    private $mockRepo;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockRepo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);
    }

    public function testGarbageCollectionEnabledByDefault(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('removeExpiredResetPasswordRequests')
            ->willReturn(1)
        ;

        $cleaner = new ResetPasswordCleaner($this->mockRepo);
        $result = $cleaner->handleGarbageCollection();

        self::assertSame(1, $result);
    }

    public function testHandleGarbageCollectionCanBeForced(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('removeExpiredResetPasswordRequests')
            ->willReturn(1)
        ;

        $cleaner = new ResetPasswordCleaner($this->mockRepo, false);
        $cleaner->handleGarbageCollection(true);
    }

    public function testGarbageCollectionCanBeDisabled(): void
    {
        $this->mockRepo
            ->expects($this->never())
            ->method('removeExpiredResetPasswordRequests')
        ;

        $cleaner = new ResetPasswordCleaner($this->mockRepo, false);
        $result = $cleaner->handleGarbageCollection();

        self::assertSame(0, $result);
    }
}
