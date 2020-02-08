<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Model;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\tests\Contract\ModelUnitTestInterface;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 */
abstract class AbstractModelUnitTest extends TestCase implements ModelUnitTestInterface
{
    /**
     * @test
     * @dataProvider propertyDataProvider
     */
    public function hasProperty(string $propertyName): void
    {
        self::assertClassHasAttribute(
            $propertyName,
            $this->sut,
            sprintf('%s does not have %s property defined.', $this->sut, $propertyName)
        );
    }

    /**
     * @test
     * @dataProvider propertyDataProvider
     * @throws \ReflectionException
     */
    public function propertyHasScope(string $propertyName, string $scope): void
    {
        $property = new \ReflectionProperty($this->sut, $propertyName);

        switch ($scope){
            case 'protected':
                $result = $property->isProtected();
                break;
            case 'public':
                $result = $property->isPublic();
                break;
            case 'private':
                $result = $property->isPrivate();
                break;
        }

        self::assertTrue($result, sprintf(
            '%s::%s visibility is expected to be declared as %s.',
            $this->sut,
            $propertyName,
            $scope
        ));
    }

    /**
     * @test
     * @dataProvider propertyDataProvider
     * @throws \ReflectionException
     */
    public function propertyHasDocBlock(string $propertyName, string $notUsed, string $docBlock): void
    {
        $property = new \ReflectionProperty($this->sut, $propertyName);
        $result = $property->getDocComment();

        self::assertStringContainsString($docBlock, $result, sprintf('%s::%s does not contain "%s" in the docBlock.', $this->sut, $propertyName, $docBlock));
    }

    /**
     * @test
     * @dataProvider methodDataProvider
     */
    public function hasMethod(string $methodName): void
    {
        self::assertTrue(
            method_exists($this->sut, $methodName),
            sprintf('%s does not have %s method defined.', $this->sut, $methodName)
        );
    }

    /**
     * @test
     * @dataProvider methodDataProvider
     * @throws \ReflectionException
     */
    public function methodHasScope(string $methodName, string $scope): void
    {
        $method = new \ReflectionMethod($this->sut, $methodName);

        switch ($scope){
            case 'protected':
                $result = $method->isProtected();
                break;
            case 'public':
                $result = $method->isPublic();
                break;
            case 'private':
                $result = $method->isPrivate();
                break;
        }

        self::assertTrue($result, sprintf(
            '%s::%s() visibility is expected to be declared as %s.',
            $this->sut,
            $methodName,
            $scope
        ));
    }
}
