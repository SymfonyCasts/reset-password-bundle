<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\FunctionalTests;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository;
use SymfonyCasts\Bundle\ResetPassword\Tests\ResetPasswordTestKernel;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordHelperTest extends TestCase
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
        $kernel = new ResetPasswordTestKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var Registry $registry */
        $registry = $container->get('doctrine');
        $this->manager = $registry->getManager();

        $this->configureDatabase();

        $this->repository = $this->manager->getRepository(ResetPasswordTestFixtureRequest::class);
    }

    public function testExpiresAt(): void
    {
        $cleaner = $this->createMock(ResetPasswordCleaner::class);
        $repo = $this->createMock(ResetPasswordRequestRepositoryInterface::class);

        $helper = new ResetPasswordHelper(
            new ResetPasswordTokenGenerator('secret', new ResetPasswordRandomGenerator()),
            $cleaner,
            $repo,
            '3600',
            '3600'
        );

        $token = $helper->generateResetToken(new ResetPasswordTestFixtureUser());

        self::assertSame(3600, ($token->getExpiresAt()->getTimestamp() - \time()));
    }

    private function configureDatabase(): void
    {
        $metaData = $this->manager->getMetadataFactory();

        $tool = new SchemaTool($this->manager);
        $tool->dropDatabase();
        $tool->createSchema($metaData->getAllMetadata());
    }
}
