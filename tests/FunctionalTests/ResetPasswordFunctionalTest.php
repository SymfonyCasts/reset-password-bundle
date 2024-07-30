<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Functional;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;
use SymfonyCasts\InternalTestHelpers\AppTestHelper;
use SymfonyCasts\InternalTestHelpers\TestProcessHelper;

final class ResetPasswordFunctionalTest extends TestCase
{
    private AppTestHelper $appTestHelper;

    protected function setUp(): void
    {
        $this->appTestHelper = (new AppTestHelper(SymfonyCastsResetPasswordBundle::class))
            ->init('symfonycasts/reset-password-bundle')
        ;
    }

    public function testAppResetPasswordWorksInAWebApp(): void
    {
        $appPath = $this->appTestHelper->createAppForTest();

        // Use MakerBundle's `make:user` to create a user because I'm too lazy to make a fixture...
        TestProcessHelper::runNow('bin/console make:user --is-entity --with-password -n --identity-property-name email User ', $appPath);

        // Copy over app fixtures that were "generated" by maker-bundle - we should replace this with make:reset-password like
        // we do above for make:user... reason for the fixtures: lazy
        $fixturesPath = \sprintf('%s/tests/Fixtures/App', $this->appTestHelper->rootPath);
        $this->appTestHelper->fs->mirror($fixturesPath, $appPath, options: ['override' => true]);

        // Setup persistence
        TestProcessHelper::runNow('bin/console d:s:create', $appPath);

        // Run the unit tests (Fixtures/App/tests) in the web app.
        TestProcessHelper::runNow('bin/phpunit', $appPath);

        // If any of the tests within the app fail, an exception is thrown and
        // "this" test will fail. But we need this assertion because we are not
        // actually performing any assertions directly.
        $this->expectNotToPerformAssertions();
    }
}
