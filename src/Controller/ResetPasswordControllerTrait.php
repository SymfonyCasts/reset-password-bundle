<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Controller;

use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
trait ResetPasswordControllerTrait
{
    private function storeTokenInSession(Request $request, ResetPasswordHelperInterface $helper, string $token): void
    {
        $request->getSession()->set($helper->getSessionTokenKey(), $token);
    }

    private function getTokenFromSession(Request $request, ResetPasswordHelperInterface $helper): string
    {
        return $request->getSession()->get($helper->getSessionTokenKey());
    }
}
