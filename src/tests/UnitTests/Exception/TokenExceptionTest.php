<?php

declare(strict_types=1);

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Exception;

use SymfonyCasts\Bundle\ResetPassword\Exception\TokenException;
use PHPUnit\Framework\TestCase;

class TokenExceptionTest extends TestCase
{
    /** @test */
    public function extendsLogicException(): void
    {
        $result = new TokenException();

        self::assertInstanceOf(\LogicException::class, $result);
    }

    public function messageDataProvider(): \Generator
    {
        yield ['getBadBytes', 'Invalid length expected. Change $size param to valid int.'];
        yield ['getIsEmpty', 'TokenGenerator::getToken() contains empty string parameter(s).'];
    }

    /**
     * @test
     * @dataProvider messageDataProvider
     */
    public function hasMessage(string $method, string $expected): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage($expected);

        throw new TokenException(TokenException::$method());
    }
}
