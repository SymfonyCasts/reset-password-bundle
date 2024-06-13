<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;
use SymfonyCasts\InternalTestHelpers\AppTestHelper;
use SymfonyCasts\InternalTestHelpers\TestProcessHelper;

class ResetPasswordFunctionalTest extends TestCase
{
    private AppTestHelper $appTestHelper;
    private string $testAppPath;

    protected function setUp(): void
    {
        $this->appTestHelper = (new AppTestHelper(SymfonyCastsResetPasswordBundle::class))
            ->init('symfonycasts/reset-password-bundle')
        ;

//        $bundleVendorPath = sprintf('%s/vendor/symfonycasts/reset-password-bundle', $this->appTestHelper->getTestAppPath());
//
//        // DX - So we can test local changes without having to commit them.
//        if (!is_link($bundleVendorPath)) {
//            $fs->remove($bundleVendorPath);
//            $fs->symlink($this->appTestHelper->getCachedBundlePath(), $bundleVendorPath);
//        }
//
//        // Ensure the app has fresh cache on the first test run
//        $fs->remove(sprintf('%s/var/cache', $this->appTestHelper->getTestAppPath()));

        $this->testAppPath = $this->appTestHelper->createAppForTest();

        // Use MakerBundle's `make:user` to create a user because I'm too lazy to make a fixture...
        TestProcessHelper::runNow('bin/console make:user --is-entity --with-password -n --identity-property-name email User ', $this->testAppPath);

        // Copy over app fixtures that were "generated" by maker-bundle - we should replace this with make:reset-password like
        // we do above for make:user... reason for the fixtures: lazy
        $fixturesPath = sprintf('%s/tests/Fixtures/App', $this->appTestHelper->rootPath);
        $this->appTestHelper->fs->mirror($fixturesPath, $this->testAppPath, options: ['override' => true]);

        // Setup persistence
        TestProcessHelper::runNow('bin/console d:s:create', $this->testAppPath);
    }

    public function testApp(): void
    {
        TestProcessHelper::runNow('bin/phpunit', $this->testAppPath);

        $this->expectNotToPerformAssertions();
    }
}
