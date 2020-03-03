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
        return 'Change repository signature for request_password_repository in config/packages/reset_password.yaml.';
    }
}
