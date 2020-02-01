<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\TokenGenerator;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\tests\Fixtures\TokenGeneratorTestFixture;

class TokenGeneratorTest extends TestCase
{
    /** @var TokenGeneratorTestFixture */
    public $fixture;

    protected function setUp()
    {
        $this->fixture = new TokenGeneratorTestFixture();
    }

    /** @test */
    public function throwsExceptionIfTokenNotInitialized(): void
    {
        $this->expectException(\Exception::class);

        $generator = new TokenGenerator();
        $generator->getToken();
    }

    /** @test */
    public function randomStrReturned(): void
    {
        $resultA = $this->fixture->getRandomAlphaNumStr(20);
        $resultB = $this->fixture->getRandomAlphaNumStr(20);

        self::assertNotSame($resultA, $resultB);
    }

    /** @test */
    public function randomStrReturnsCorrectLength(): void
    {
        $result = $this->fixture->getRandomAlphaNumStr(100);

        self::assertSame(100, strlen($result));
    }

    /** @test */
    public function RandomBytesThrowsExceptionWithBadSize(): void
    {
        $this->expectException(\Error::class);
        $this->fixture->getRandomBytesFromProtected(0);
    }

    /** @test */
    public function getRandomBytesUsesLength(): void
    {
        $result = $this->fixture->getRandomBytesFromProtected(100);

        $this->assertSame(200, strlen(bin2hex($result)));
    }
}
