<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\Fixtures;

use SymfonyCasts\Bundle\ResetPassword\Persistence\PasswordResetRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\PasswordResetRequestRepositoryTrait;

class PWResetRequestRepositoryTraitTestFixture implements PasswordResetRequestRepositoryInterface
{
    use PasswordResetRequestRepositoryTrait;
}
