<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\tests\Contract;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 */
interface ModelUnitTestInterface
{
    public function propertyDataProvider(): \Generator;

    public function hasProperty(string $propertyName): void;

    public function propertyHasScope(string $propertyName, string $scope): void;

    public function propertyHasDocBlock(string $propertyName, string $scopeNotUsedInTest, string $docBlock): void;

    public function methodDataProvider(): \Generator;

    public function hasMethod(string $methodName): void;

    public function methodHasScope(string $methodName, string $scope): void;
}
