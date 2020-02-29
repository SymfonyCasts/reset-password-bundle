<?php

namespace SymfonyCasts\Bundle\ResetPassword;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonyCasts\Bundle\ResetPassword\DependencyInjection\SymfonyCastsResetPasswordExtension;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class SymfonyCastsResetPasswordBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new SymfonyCastsResetPasswordExtension();
        }

        return $this->extension ?: null;
    }
}
