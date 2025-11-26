<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Fake\FakeResetPasswordInternalRepository;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('symfonycasts.reset_password.fake_request_repository', FakeResetPasswordInternalRepository::class)
        ->private();

    $services->set('symfonycasts.reset_password.cleaner', ResetPasswordCleaner::class)
        ->private()
        ->args([
            '', // reset password request persister
            '', // reset password request enable_garbage_collection
        ]);

    $services->set(ResetPasswordRemoveExpiredCommand::class)
        ->args([service('symfonycasts.reset_password.cleaner')])
        ->tag('console.command', ['command' => 'reset-password:remove-expired']);

    $services->set('symfonycasts.reset_password.random_generator', ResetPasswordRandomGenerator::class)
        ->private();

    $services->set('symfonycasts.reset_password.token_generator', ResetPasswordTokenGenerator::class)
        ->private()
        ->args([
            '%kernel.secret%',
            service('symfonycasts.reset_password.random_generator'),
        ]);

    $services->alias(ResetPasswordHelperInterface::class, 'symfonycasts.reset_password.helper');

    $services->set('symfonycasts.reset_password.helper', ResetPasswordHelper::class)
        ->args([
            service('symfonycasts.reset_password.token_generator'),
            service('symfonycasts.reset_password.cleaner'),
            '', // reset password request persister
            '', // reset password request lifetime
            '', // reset password throttle limit
        ]);
};
