<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetToken;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface PasswordResetHelperInterface
{
    public function generateResetToken(UserInterface $user): PasswordResetToken;

    public function validateTokenAndFetchUser(string $fullToken): UserInterface;

    public function removeResetRequest(string $fullToken): void;
}
