<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
