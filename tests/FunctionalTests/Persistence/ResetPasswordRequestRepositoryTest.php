<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\FunctionalTests\Persistence;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\AbstractResetPasswordTestKernel;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordRequestTestFixture;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordUserTestFixture;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordRequestRepositoryTestFixture;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordRequestRepositoryTest extends TestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    private $manager;

    protected function setUp(): void
    {
        $kernel = new ResetPasswordFunctionalKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var Registry $registry */
        $registry = $container->get('doctrine');
        $this->manager = $registry->getManager();

        $this->configureDatabase();
    }

    public function testPersistResetPasswordRequestPersistsRequestObject(): void
    {
        $fixture = new ResetPasswordRequestTestFixture();
        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $repo->persistResetPasswordRequest($fixture);

        $result = $repo->findAll();
        self::assertSame($fixture, $result[0]);
    }

    public function testGetUserIdentifierRetrievesObjectIdFromPersistence(): void
    {
        $fixture = new ResetPasswordRequestTestFixture();

        $this->manager->persist($fixture);
        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $result = $repo->getUserIdentifier($fixture);

        self::assertSame('1', $result);
    }

    public function testFindResetPasswordRequestReturnsObjectWithGivenSelector(): void
    {
        $fixture = new ResetPasswordRequestTestFixture();
        $fixture->selector = '1234';

        $this->manager->persist($fixture);
        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $result = $repo->findResetPasswordRequest('1234');

        self::assertSame($fixture, $result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsExpected(): void
    {
        $userFixture = new ResetPasswordUserTestFixture();
        $this->manager->persist($userFixture);

        $fixtureOld = new ResetPasswordRequestTestFixture();
        $fixtureOld->requestedAt = new \DateTimeImmutable('-5 minutes');
        $fixtureOld->user = $userFixture;
        $this->manager->persist($fixtureOld);

        $expectedTime = new \DateTimeImmutable();
        $fixtureNewest = new ResetPasswordRequestTestFixture();
        $fixtureNewest->expiresAt = new \DateTimeImmutable('+1 hours');
        $fixtureNewest->requestedAt = $expectedTime;
        $fixtureNewest->user = $userFixture;
        $this->manager->persist($fixtureNewest);

        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $result = $repo->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertSame($expectedTime, $result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsNullOnExpiredRequest(): void
    {
        $userFixture = new ResetPasswordUserTestFixture();
        $this->manager->persist($userFixture);

        $expiredFixture = new ResetPasswordRequestTestFixture();
        $expiredFixture->user = $userFixture;
        $expiredFixture->expiresAt = new \DateTimeImmutable('-1 hours');
        $expiredFixture->requestedAt = new\DateTimeImmutable('-2 hours');
        $this->manager->persist($expiredFixture);

        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $result = $repo->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertNull($result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsNullIfRequestNotFound(): void
    {
        $userFixture = new ResetPasswordUserTestFixture();
        $this->manager->persist($userFixture);

        $fixture = new ResetPasswordRequestTestFixture();
        $this->manager->persist($fixture);

        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $result = $repo->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertNull($result);
    }

    public function testRemoveResetPasswordRequestRemovedGivenObjectFromPersistence(): void
    {
        $fixture = new ResetPasswordRequestTestFixture();

        $this->manager->persist($fixture);
        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $repo->removeResetPasswordRequest($fixture);

        $this->assertCount(0, $repo->findAll());
    }

    public function testRemovedExpiredResetPasswordRequestsOnlyRemovedExpiredRequestsFromPersistence(): void
    {
        $expiredFixture = new ResetPasswordRequestTestFixture();
        $expiredFixture->expiresAt = (new \DateTimeImmutable())
            ->modify('-9999999 seconds')
        ;
        $this->manager->persist($expiredFixture);

        $futureFixture = new ResetPasswordRequestTestFixture();
        $this->manager->persist($futureFixture);

        $this->manager->flush();

        /** @var ResetPasswordRequestRepositoryTestFixture $repo */
        $repo = $this->manager->getRepository(ResetPasswordRequestTestFixture::class);
        $repo->removeExpiredResetPasswordRequests();

        $result = $repo->findAll();

        self::assertCount(1, $result);
        self::assertSame($futureFixture, $result[0]);
    }

    private function configureDatabase(): void
    {
        $metaData = $this->manager->getMetadataFactory();

        $tool = new SchemaTool($this->manager);
        $tool->dropDatabase();
        $tool->createSchema($metaData->getAllMetadata());
    }
}

class ResetPasswordFunctionalKernel extends AbstractResetPasswordTestKernel
{
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        parent::configureContainer($container, $loader);
        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'url' => 'sqlite:///fake'
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'App' =>[
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/tests/Fixtures/Entity/',
                        'prefix' => 'SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity',
                        'alias' => 'App'
                    ]
                ]
            ]
        ]);
    }
}
