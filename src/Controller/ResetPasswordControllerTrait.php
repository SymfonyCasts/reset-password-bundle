<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Provides useful methods to a "reset password controller"
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
trait ResetPasswordControllerTrait
{
    private function setCanCheckEmailInSession(Request $request, bool $value = true): void
    {
        $request->getSession()->set('ResetPasswordCheckEmail', $value);
    }

    private function isAbleToCheckEmail(SessionInterface $session): bool
    {
        $sessionKey = 'ResetPasswordCheckEmail';

        if ($session->get($sessionKey)) {
            $session->remove($sessionKey);

            return true;
        }

        return false;
    }

    private function storeTokenInSession(Request $request, string $token): void
    {
        $request->getSession()->set('ResetPasswordPublicToken', $token);
    }

    private function getTokenFromSession(Request $request): string
    {
        return $request->getSession()->get('ResetPasswordPublicToken');
    }
}
