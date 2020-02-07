<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\Fixtures;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\PasswordResetRequestRepositoryTrait;

class PWResetRequestRepositoryTraitTestFixture implements PasswordResetRequestRepositoryInterface
{
    use PasswordResetRequestRepositoryTrait;

    // createPasswordResetRequest() is a userland method.. To be reated by maker?
    public function createPasswordResetRequest(
        UserInterface $user,
        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ): PasswordResetRequestInterface {
    }
}
