<?php

namespace SymfonyCasts\Bundle\ResetPasswordTests\UnitTests\Model;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * @author  Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordRequestTraitTest extends TestCase
{
    private const SUT = ResetPasswordRequestTrait::class;

    /**
     * @var \DateTimeImmutable
     */
    private $expiresAt;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->expiresAt = $this->createMock(\DateTimeImmutable::class);
    }

    private function getFixture(): ResetPasswordRequestInterface
    {
        return new class ($this->expiresAt, '', '') implements ResetPasswordRequestInterface
        {
            use ResetPasswordRequestTrait;

            public function __construct($expiresAt, $selector, $token)
            {
                $this->initialize($expiresAt, $selector, $token);
            }

            /**
             * getUser() is intentionally left out of the trait.
             * it is created via maker under App\Entity\PasswordResetRequest
             * as the user property, specifically its target entity,
             * is unknown to the ResetPassword bundle. Although getUser()
             * could be added to the trait within the bundle, for clarity
             * sake, the maker creates the method.
             **/

            public function getUser()
            {
            }
        };
    }

    public function testIsCompatibleWithInterface(): void
    {
        $sut = $this->getFixture();

        self::assertInstanceOf(ResetPasswordRequestInterface::class, $sut);
    }

    public function propertyDataProvider(): \Generator
    {
        yield ['selector', '@ORM\Column(type="string", length=100)'];
        yield ['hashedToken', '@ORM\Column(type="string", length=100)'];
        yield ['requestedAt', '@ORM\Column(type="datetime_immutable")'];
        yield ['expiresAt', '@ORM\Column(type="datetime_immutable")'];
    }

    /**
     * @dataProvider propertyDataProvider
     */
    public function testORMAnnotationSetOnProperty(string $propertyName, string $expectedAnnotation): void
    {
        $property = new \ReflectionProperty(self::SUT, $propertyName);
        $result = $property->getDocComment();

        self::assertStringContainsString($expectedAnnotation, $result, sprintf('%s::%s does not contain "%s" in the docBlock.', self::SUT, $propertyName, $expectedAnnotation));
    }

    public function testIsExpiredReturnsFalseWithTimeInFuture(): void
    {
        $this->expiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(time() + (360))
        ;

        $trait = $this->getFixture();
        self::assertFalse($trait->isExpired());
    }

    public function testIsExpiredReturnsTrueWithTimeInPast(): void
    {
        $this->expiresAt
            ->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(time() - (360))
        ;

        $trait = $this->getFixture();
        self::assertTrue($trait->isExpired());
    }
}
