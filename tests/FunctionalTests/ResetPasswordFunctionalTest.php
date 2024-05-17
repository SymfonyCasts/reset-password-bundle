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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ResetPasswordFunctionalTest extends TestCase
{
    private string $appPath;

    protected function setUp(): void
    {
        $fs = new Filesystem();
        $rootPath = realpath(\dirname(__DIR__, 2));
        $cachePath = sprintf('%s/tests/tmp/cache', $rootPath);
        $this->appPath = sprintf('%s/app', $cachePath);
        $bundlePath = sprintf('%s/bundle', $cachePath);

        if ($fs->exists($cachePath)) {
            $fs->remove($cachePath);
        }

        $fs->mkdir($cachePath);

        // Copy bundle to a "repo" dir for tests
        $this->runProcess(sprintf('git clone %s %s/bundle', $rootPath, $cachePath), $cachePath);

        // Install Symfony Skeleton
        $this->runProcess(sprintf('composer create-project symfony/skeleton %s --prefer-dist', $this->appPath), $cachePath);

        // Setup the app as a "webapp" (similar to symfony new --webapp)
        $this->runProcess('composer require symfony/webapp-pack --prefer-dist', $this->appPath);

        // Tell composer to use "our" reset-password-bundle instead of fetching it from packagist.
        $composerJson = json_decode(file_get_contents(sprintf('%s/composer.json', $this->appPath)), associative: true, flags: \JSON_THROW_ON_ERROR);

        $composerJson['repositories']['symfonycasts/reset-password-bundle'] = [
            'type' => 'git',
            'url' => $bundlePath,
            'options' => [
                'versions' => [
                    'symfonycasts/reset-password-bundle' => '9999.99',
                ],
            ],
        ];

        file_put_contents(sprintf('%s/composer.json', $this->appPath), json_encode($composerJson, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));

        $this->runProcess('composer require symfonycasts/reset-password-bundle', $this->appPath);

        $bundleVendorPath = sprintf('%s/vendor/symfonycasts/reset-password-bundle', $this->appPath);

        // DX - So we can test local changes without having to commit them.
        if (!is_link($bundleVendorPath)) {
            $fs->remove($bundleVendorPath);
            $fs->symlink($bundlePath, $bundleVendorPath);
        }

        // Ensure the app has fresh cache on the first test run
        $fs->remove(sprintf('%s/var/cache', $this->appPath));

        // Use MakerBundle's `make:user` to create a user because I'm too lazy to make a fixture...
        $this->runProcess('bin/console make:user --is-entity --with-password -n --identity-property-name email User ', $this->appPath);

        // Copy over app fixtures that were "generated" by maker-bundle - we should replace this with make:reset-password like
        // we do above for make:user... reason for the fixtures: lazy
        $fixturesPath = sprintf('%s/tests/Fixtures/App', $rootPath);
        $fs->mirror($fixturesPath, $this->appPath, options: ['override' => true]);

        // Setup persistence
        $this->runProcess('bin/console d:s:create', $this->appPath);
    }

    private function runProcess(string $cmd, string $workingDir): void
    {
        $process = Process::fromShellCommandline($cmd, $workingDir);

        if (0 !== ($exitCode = $process->run())) {
            dump($process->getErrorOutput());

            throw new \RuntimeException(sprintf('Process Failed - Exit Code: %d - %s Cmd: %s', $exitCode, Process::$exitCodes[$exitCode] ?? '', $cmd));
        }
    }

    public function testApp(): void
    {
        try {
            // Run test app tests
            Process::fromShellCommandline('bin/phpunit', $this->appPath)
                ->mustRun();
        } catch (ProcessFailedException $exception) {
            $this->fail($exception->getMessage());
        }

        $this->expectNotToPerformAssertions();
    }
}
