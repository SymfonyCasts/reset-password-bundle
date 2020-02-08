<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\PWResetPasswordRequestRepositoryTraitTestFixture;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\PasswordResetRequestRepositoryTrait;

class PWResetRequestRepositoryTraitTest extends TestCase
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
        self::assertTrue(method_exists(PasswordResetRequestRepositoryTrait::class, $method));
    }

    /** @test */
    public function traitIsCompatibleWithInterface(): void
    {
        // Ensure fixture implements ResetPasswordRequestRepositoryInterface::class
        $interfaces = class_implements(PWResetPasswordRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(ResetPasswordRequestRepositoryInterface::class, $interfaces);

        // Ensure fixture uses PasswordResetRequestRepositoryTrait::class
        $traits = class_uses(PWResetPasswordRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(PasswordResetRequestRepositoryTrait::class, $traits);

        // PHP throws fatal error if trait is not compatible with interface
        new PWResetPasswordRequestRepositoryTraitTestFixture();
    }
}
