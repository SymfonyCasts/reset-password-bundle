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

    protected function setUp(): void
    {
        $this->appTestHelper = new AppTestHelper(SymfonyCastsResetPasswordBundle::class);
        $fs = new Filesystem();

        if ($fs->exists($this->appTestHelper->getCachePath())) {
            $fs->remove($this->appTestHelper->getCachePath());
        }

        $fs->mkdir($this->appTestHelper->getCachePath());

        // Copy bundle to a "repo" dir for tests
        TestProcessHelper::runNow(sprintf('git clone %s %s', $this->appTestHelper->getRootPath(), $this->appTestHelper->getCachedBundlePath()), $this->appTestHelper->getCachePath());

        // Install Symfony Skeleton
        TestProcessHelper::runNow(sprintf('composer create-project symfony/skeleton %s --prefer-dist', $this->appTestHelper->getTestAppPath()), $this->appTestHelper->getCachePath());

        // Setup the app as a "webapp" (similar to symfony new --webapp)
        TestProcessHelper::runNow('composer require symfony/webapp-pack --prefer-dist', $this->appTestHelper->getTestAppPath());

        // Tell composer to use "our" reset-password-bundle instead of fetching it from packagist.
        $composerJson = json_decode(file_get_contents(sprintf('%s/composer.json', $this->appTestHelper->getTestAppPath())), associative: true, flags: \JSON_THROW_ON_ERROR);

        $composerJson['repositories']['symfonycasts/reset-password-bundle'] = [
            'type' => 'path',
            'url' => $this->appTestHelper->getCachedBundlePath(),
            'options' => [
                'versions' => [
                    'symfonycasts/reset-password-bundle' => '9999.99',
                ],
            ],
        ];

        file_put_contents(sprintf('%s/composer.json', $this->appTestHelper->getTestAppPath()), json_encode($composerJson, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));

        TestProcessHelper::runNow('composer require symfonycasts/reset-password-bundle', $this->appTestHelper->getTestAppPath());

        $bundleVendorPath = sprintf('%s/vendor/symfonycasts/reset-password-bundle', $this->appTestHelper->getTestAppPath());

        // DX - So we can test local changes without having to commit them.
        if (!is_link($bundleVendorPath)) {
            $fs->remove($bundleVendorPath);
            $fs->symlink($this->appTestHelper->getCachedBundlePath(), $bundleVendorPath);
        }

        // Ensure the app has fresh cache on the first test run
        $fs->remove(sprintf('%s/var/cache', $this->appTestHelper->getTestAppPath()));

        // Use MakerBundle's `make:user` to create a user because I'm too lazy to make a fixture...
        TestProcessHelper::runNow('bin/console make:user --is-entity --with-password -n --identity-property-name email User ', $this->appTestHelper->getTestAppPath());

        // Copy over app fixtures that were "generated" by maker-bundle - we should replace this with make:reset-password like
        // we do above for make:user... reason for the fixtures: lazy
        $fixturesPath = sprintf('%s/tests/Fixtures/App', $this->appTestHelper->getRootPath());
        $fs->mirror($fixturesPath, $this->appTestHelper->getTestAppPath(), options: ['override' => true]);

        // Setup persistence
        TestProcessHelper::runNow('bin/console d:s:create', $this->appTestHelper->getTestAppPath());
    }

    public function testApp(): void
    {
        TestProcessHelper::runNow('bin/phpunit', $this->appTestHelper->getTestAppPath());

        $this->expectNotToPerformAssertions();
    }
}
