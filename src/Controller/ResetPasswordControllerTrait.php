<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Provides useful methods to a "reset password controller"
 *
 * Use of this trait requires a controller to extend
 * Symfony\Bundle\FrameworkBundle\Controller\AbstractController
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
trait ResetPasswordControllerTrait
{
    private function setCanCheckEmailInSession(): void
    {
        $this->getSession()->set('ResetPasswordCheckEmail', true);
    }

    private function isAbleToCheckEmail(): bool
    {
        $sessionKey = 'ResetPasswordCheckEmail';
        $session = $this->getSession();

        if ($session->get($sessionKey)) {
            $session->remove($sessionKey);

            return true;
        }

        return false;
    }

    private function storeTokenInSession(string $token): void
    {
        $this->getSession()->set('ResetPasswordPublicToken', $token);
    }

    private function getTokenFromSession(): string
    {
        return $this->getSession()->get('ResetPasswordPublicToken');
    }

    private function getSession(): SessionInterface
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $request->getSession();
    }
}
