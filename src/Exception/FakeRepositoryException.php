<?php

namespace SymfonyCasts\Bundle\ResetPassword\Exception;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class FakeRepositoryException extends \Exception implements ResetPasswordExceptionInterface
{
    public function getReason(): string
    {
        return 'Please update the request_password_repository configuration in config/packages/reset_password.yaml to point to your "request password repository` service.';
    }
}
