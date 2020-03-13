<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\UnitTests\Persistence;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Exception\FakeRepositoryException;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Fake\FakeResetPasswordInternalRepository;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class FakeResetPasswordInternalRepositoryTest extends TestCase
{
    public function methodDataProvider(): \Generator
    {
        yield ['createResetPasswordRequest', [new \stdClass(), new \DateTimeImmutable(), '', '']];
        yield ['getUserIdentifier', [new \stdClass()]];
        yield ['persistResetPasswordRequest', [$this->createMock(ResetPasswordRequestInterface::class)]];
        yield ['findResetPasswordRequest', ['']];
        yield ['getMostRecentNonExpiredRequestDate', [new \stdClass()]];
        yield ['removeResetPasswordRequest', [$this->createMock(ResetPasswordRequestInterface::class)]];
    }

    /**
     * @dataProvider methodDataProvider
     */
    public function testAllMethodsThrowFakeRepositoryException(string $methodName, array $params): void
    {
        $repo = new FakeResetPasswordInternalRepository();

        $this->expectException(FakeRepositoryException::class);

        $repo->$methodName(...$params);
    }
}
