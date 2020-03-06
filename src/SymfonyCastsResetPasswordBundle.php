<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
