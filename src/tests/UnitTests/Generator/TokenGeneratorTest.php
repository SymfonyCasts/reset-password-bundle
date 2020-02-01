<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    /** @test */
    public function throwsExceptionIfTokenNotInitialized(): void
    {
        $this->expectException(\Exception::class);

        $generator = new TokenGenerator();
        $generator->getToken();
    }
}
