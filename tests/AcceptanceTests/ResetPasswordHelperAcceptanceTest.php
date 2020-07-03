<?php

/*
 * This file is part of the SymfonyCasts ResetPasswordBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\ResetPassword\Tests\AcceptanceTests;

use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureRequest;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity\ResetPasswordTestFixtureUser;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureRequestRepository;
use SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\ResetPasswordTestFixtureUserRepository;
use SymfonyCasts\Bundle\ResetPassword\Tests\ResetPasswordTestKernel;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
final class ResetPasswordHelperAcceptanceTest extends TestCase
{
    /** @var ContainerInterface */
    private $container;
    /**
     * @var ObjectManager|object
     */
    private $manager;

    /**
     * @var ResetPasswordTestFixtureRequestRepository
     */
    private $resetRepo;

    /**
     * @var ResetPasswordTestFixtureUserRepository
     */
    private $userRepo;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $kernel = $this->getBootedKernel();

        $this->container = $kernel->getContainer();

        /** @var Registry $registry */
        $registry = $this->container->get('doctrine');
        $this->manager = $registry->getManager();

        $this->configureDatabase();

        $this->resetRepo = $this->manager->getRepository(ResetPasswordTestFixtureRequest::class);
        $this->userRepo = $this->manager->getRepository(ResetPasswordTestFixtureUser::class);
    }

    public function testExpiredAtNotAffectedByTimezones(): void
    {
        $currentTz = \date_default_timezone_get();

        /** @var ResetPasswordHelper $helper */
        $helper = $this->container->get(ResetPasswordAcceptanceFixture::class)->helper;

        $user = new ResetPasswordTestFixtureUser();

        $this->manager->persist($user);
        $this->manager->flush();

        $token = $helper->generateResetToken($user);

        foreach (DateTimeZone::listIdentifiers() as $tz) {
            \date_default_timezone_set($tz);
            $request = $this->resetRepo->findResetPasswordRequest(
                \substr($token->getToken(), 0, 20)
            );

            self::assertInstanceOf(
                ResetPasswordTestFixtureRequest::class,
                $request
            );

            self::assertFalse($request->isExpired());

            self::assertInstanceOf(ResetPasswordToken::class, $token);
        }

        \date_default_timezone_set($currentTz);
    }

    public function testExpiredAtNotAffectedByTimezonesChangeX(): void
    {
        $currentTz = \date_default_timezone_get();

        /** @var ResetPasswordHelper $helper */
        $helper = $this->container->get(ResetPasswordAcceptanceFixture::class)->helper;

        $user = new ResetPasswordTestFixtureUser();

        $this->manager->persist($user);
        $this->manager->flush();

        \date_default_timezone_set('America/Anchorage');
        $token = $helper->generateResetToken($user);

        foreach (DateTimeZone::listIdentifiers() as $tz) {
            \date_default_timezone_set($tz);
            $request = $this->resetRepo->findResetPasswordRequest(
                \substr($token->getToken(), 0, 20)
            );

            self::assertInstanceOf(
                ResetPasswordTestFixtureRequest::class,
                $request
            );

            self::assertFalse($request->isExpired());

            self::assertInstanceOf(ResetPasswordToken::class, $token);
        }

        \date_default_timezone_set($currentTz);
    }

    private function getBootedKernel(): KernelInterface
    {
        $builder = new ContainerBuilder();
        $builder->autowire(ResetPasswordAcceptanceFixture::class)
            ->setPublic(true)
        ;

        $kernel = new ResetPasswordTestKernel(
            $builder,
            ['reset-test' => '/reset-password']
        );

        $kernel->boot();

        return $kernel;
    }

    private function configureDatabase(): void
    {
        $metaData = $this->manager->getMetadataFactory();

        $tool = new SchemaTool($this->manager);
        $tool->dropDatabase();
        $tool->createSchema($metaData->getAllMetadata());
    }
}

class ResetPasswordAcceptanceFixture
{
    public $helper;

    public function __construct(ResetPasswordHelperInterface $helper)
    {
        $this->helper = $helper;
    }
}
