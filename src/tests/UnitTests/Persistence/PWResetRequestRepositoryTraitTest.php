<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Persistence;

use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\PWResetRequestRepositoryTraitTestFixture;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\PasswordResetRequestRepositoryTrait;

class PWResetRequestRepositoryTraitTest extends TestCase
{
    public function methodDataProvider(): \Generator
    {
        yield ['persistPasswordResetRequest'];
        yield ['findPasswordResetRequest'];
        yield ['getMostRecentNonExpiredRequestDate'];
        yield ['removeResetRequest'];
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
        // Ensure fixture implements PasswordResetRequestRepositoryInterface::class
        $interfaces = class_implements(PWResetRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(PasswordResetRequestRepositoryInterface::class, $interfaces);

        // Ensure fixture uses PasswordResetRequestRepositoryTrait::class
        $traits = class_uses(PWResetRequestRepositoryTraitTestFixture::class);
        self::assertArrayHasKey(PasswordResetRequestRepositoryTrait::class, $traits);

        // PHP throws fatal error if trait is not compatible with interface
        new PWResetRequestRepositoryTraitTestFixture();
    }
}
