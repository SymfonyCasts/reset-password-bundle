<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

/**
 * Provides useful methods to a "reset password controller".
 *
 * Use of this trait requires a controller to extend
 * Symfony\Bundle\FrameworkBundle\Controller\AbstractController
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
trait ResetPasswordControllerTrait
{
    private function storeTokenInSession(string $token): void
    {
        $this->getSessionService()->set('ResetPasswordPublicToken', $token);
    }

    private function getTokenFromSession(): ?string
    {
        return $this->getSessionService()->get('ResetPasswordPublicToken');
    }

    private function setTokenObjectInSession(ResetPasswordToken $token): void
    {
        $token->clearToken();

        $this->getSessionService()->set('ResetPasswordToken', $token);
    }

    private function getTokenObjectFromSession(): ?ResetPasswordToken
    {
        return $this->getSessionService()->get('ResetPasswordToken');
    }

    private function cleanSessionAfterReset(): void
    {
        $session = $this->getSessionService();

        $session->remove('ResetPasswordPublicToken');
        $session->remove('ResetPasswordCheckEmail');
        $session->remove('ResetPasswordToken');
    }

    private function getSessionService(): SessionInterface
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $request->getSession();
    }
}
