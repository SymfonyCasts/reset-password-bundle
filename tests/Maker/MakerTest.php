<?php

namespace SymfonyCasts\Bundle\ResetPassword\Tests\Maker;

use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;
use SymfonyCasts\Bundle\ResetPassword\MakerBundle\MakeResetPassword;

class MakerTest extends MakerTestCase
{
    public function getTestDetails(): \Generator
    {
        yield 'it_does_something' => [$this->createMakerTest()
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker([]);

                self::assertStringContainsString('Let\'s make a password reset feature!', $output);
            })
        ];
    }

    protected function getMakerClass(): string
    {
        return MakeResetPassword::class;
    }
}
