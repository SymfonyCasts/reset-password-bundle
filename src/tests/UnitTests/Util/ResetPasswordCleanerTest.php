<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\tests\tests\UnitTests\Util;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;
use PHPUnit\Framework\TestCase;

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
        $result = $cleaner->removeExpiredRequests();

        self::assertSame(1, $result);
    }
}
