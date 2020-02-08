<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\ResetPasswordRequestRepositoryTraitTestFixture;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;

class ResetPasswordRequestRepositoryTraitTest extends TestCase
{
    public function methodDataProvider(): \Generator
    {
        yield ['persistResetPasswordRequest'];
        yield ['findResetPasswordRequest'];
        yield ['getMostRecentNonExpiredRequestDate'];
        yield ['removeResetPasswordRequest'];
    }

    /**
     * @test
     * @dataProvider methodDataProvider
     */
    public function hasMethod(string $method): void
    {
        self::assertTrue(method_exists(ResetPasswordRequestRepositoryTrait::class, $method));
    }

    /** @test */
    public function traitIsCompatibleWithInterface(): void
    {
        // Ensure fixture implements ResetPasswordRequestRepositoryInterface::class
        $interfaces = class_implements(ResetPasswordRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(ResetPasswordRequestRepositoryInterface::class, $interfaces);

        // Ensure fixture uses ResetPasswordRequestRepositoryTrait::class
        $traits = class_uses(ResetPasswordRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(ResetPasswordRequestRepositoryTrait::class, $traits);

        // PHP throws fatal error if trait is not compatible with interface
        new ResetPasswordRequestRepositoryTraitTestFixture();
    }
}
