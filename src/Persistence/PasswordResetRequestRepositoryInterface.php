<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Persistence;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\PasswordResetRequestInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
interface PasswordResetRequestRepositoryInterface
{
    public function createPasswordResetRequest(UserInterface $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): PasswordResetRequestInterface;

    public function getUserIdentifier(object $user): string;

    public function persistPasswordResetRequest(PasswordResetRequestInterface $passwordResetRequest);

    public function findPasswordResetRequest(string $selector): ?PasswordResetRequestInterface;

    public function getMostRecentNonExpiredRequestDate(UserInterface $user): ?\DateTimeInterface;

    public function removeResetRequest(PasswordResetRequestInterface $resetRequest): void;
}
