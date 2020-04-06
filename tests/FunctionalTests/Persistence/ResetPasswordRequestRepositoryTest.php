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
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\AbstractResetPasswordTestKernel;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 *
 * @internal
 */
final class ResetPasswordRequestRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager|object
     */
    private $manager;

    /**
     * @var ResetPasswordTestFixtureRequestRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $kernel = new ResetPasswordFunctionalKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var Registry $registry */
        $registry = $container->get('doctrine');
        $this->manager = $registry->getManager();

        $this->configureDatabase();

        $this->repository = $this->manager->getRepository(ResetPasswordTestFixtureRequest::class);
    }

    public function testPersistResetPasswordRequestPersistsRequestObject(): void
    {
        $fixture = new ResetPasswordTestFixtureRequest();

        $this->repository->persistResetPasswordRequest($fixture);

        $result = $this->repository->findAll();

        self::assertSame($fixture, $result[0]);
    }

    public function testGetUserIdentifierRetrievesObjectIdFromPersistence(): void
    {
        $fixture = new ResetPasswordTestFixtureRequest();

        $this->manager->persist($fixture);
        $this->manager->flush();

        $result = $this->repository->getUserIdentifier($fixture);

        self::assertSame('1', $result);
    }

    public function testFindResetPasswordRequestReturnsObjectWithGivenSelector(): void
    {
        $fixture = new ResetPasswordTestFixtureRequest();
        $fixture->selector = '1234';

        $this->manager->persist($fixture);
        $this->manager->flush();

        $result = $this->repository->findResetPasswordRequest('1234');

        self::assertSame($fixture, $result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsExpected(): void
    {
        $userFixture = new ResetPasswordTestFixtureUser();

        $this->manager->persist($userFixture);

        $fixtureOld = new ResetPasswordTestFixtureRequest();
        $fixtureOld->requestedAt = new \DateTimeImmutable('-5 minutes');
        $fixtureOld->user = $userFixture;

        $this->manager->persist($fixtureOld);

        $expectedTime = new \DateTimeImmutable();

        $fixtureNewest = new ResetPasswordTestFixtureRequest();
        $fixtureNewest->expiresAt = new \DateTimeImmutable('+1 hours');
        $fixtureNewest->requestedAt = $expectedTime;
        $fixtureNewest->user = $userFixture;

        $this->manager->persist($fixtureNewest);
        $this->manager->flush();

        $result = $this->repository->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertSame($expectedTime, $result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsNullOnExpiredRequest(): void
    {
        $userFixture = new ResetPasswordTestFixtureUser();

        $this->manager->persist($userFixture);

        $expiredFixture = new ResetPasswordTestFixtureRequest();
        $expiredFixture->user = $userFixture;
        $expiredFixture->expiresAt = new \DateTimeImmutable('-1 hours');
        $expiredFixture->requestedAt = new\DateTimeImmutable('-2 hours');

        $this->manager->persist($expiredFixture);
        $this->manager->flush();

        $result = $this->repository->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertNull($result);
    }

    public function testGetMostRecentNonExpiredRequestDateReturnsNullIfRequestNotFound(): void
    {
        $userFixture = new ResetPasswordTestFixtureUser();

        $this->manager->persist($userFixture);
        $this->manager->persist(new ResetPasswordTestFixtureRequest());
        $this->manager->flush();

        $result = $this->repository->getMostRecentNonExpiredRequestDate($userFixture);

        self::assertNull($result);
    }

    public function testRemoveResetPasswordRequestRemovedGivenObjectFromPersistence(): void
    {
        $fixture = new ResetPasswordTestFixtureRequest();

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->repository->removeResetPasswordRequest($fixture);

        $this->assertCount(0, $this->repository->findAll());
    }

    public function testRemovedExpiredResetPasswordRequestsOnlyRemovedExpiredRequestsFromPersistence(): void
    {
        $expiredFixture = new ResetPasswordTestFixtureRequest();
        $expiredFixture->expiresAt = new \DateTimeImmutable('-2 hours');

        $this->manager->persist($expiredFixture);

        $futureFixture = new ResetPasswordTestFixtureRequest();

        $this->manager->persist($futureFixture);
        $this->manager->flush();

        $this->repository->removeExpiredResetPasswordRequests();

        $result = $this->repository->findAll();

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
}
