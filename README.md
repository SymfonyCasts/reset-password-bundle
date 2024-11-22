# ResetPasswordBundle: Mind-Blowing (and Secure) Password Resetting for Symfony

[![CI](https://github.com/SymfonyCasts/reset-password-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/SymfonyCasts/reset-password-bundle/actions/workflows/ci.yaml)

Worrying about how to deal with users that can't remember their password? We've 
got you covered! This bundle provides a secure out of the box solution to allow 
users to reset their forgotten passwords.

## Installation

The bundle can be installed using Composer or the [Symfony binary](https://symfony.com/download):

```
composer require symfonycasts/reset-password-bundle
```

## Usage

There are two ways to get started, the easiest and preferred way is to use 
Symfony's [MakerBundle](https://github.com/symfony/maker-bundle). The Maker will
 take care of everything from creating configuration, to generating your 
 templates, controllers, and entities.

### Using Symfony's Maker Bundle (Recommended)

- Run `bin/console make:reset-password`, answer a couple questions, and enjoy our bundle!

### Setting things up manually

If you prefer to take care of the leg work yourself, checkout the 
[manual setup](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/docs/manual-setup.md) 
guide. We still recommend using the Maker command to get a feel for how we 
intended the bundle to be used.

---

If you used our Symfony Maker command `bin/console make:reset-password` after 
installation, your app is ready to go. Go to `https://your-apps-domain/reset-password`, 
fill out the form, click on the link sent to your email, and change your password. 
That's it! The ResetPasswordBundle takes care of the rest.

The above assumes you have already setup 
[authentication](https://symfony.com/doc/current/security.html) with a 
registered user account & configured Symfony's 
[mailer](https://symfony.com/doc/current/mailer.html) in your app.

## Configuration

You can change the default configuration parameters for the bundle in the 
`config/packages/reset_password.yaml` config file created by Maker.

```yaml
symfonycasts_reset_password:
    request_password_repository: App\Repository\ResetPasswordRequestRepository
    lifetime: 3600
    throttle_limit: 3600
    enable_garbage_collection: true
```

The production environment may require the `default_uri` to be defined in the `config/packages/routing.yaml` to prevent the URI in emails to point to localhost.

```yaml
# config/packages/routing.yaml
when@prod:
    framework:
        router:
            # ...
            default_uri: '<your project's root URI>'
```

### Parameters:

#### `request_password_repository`

_Required_

The complete namespace of the repository for the `ResetPasswordRequest` entity. If
 you used `make:reset-password`, this will be `App\Repository\ResetPasswordRequestRepository`.

#### `lifetime`

_Optional_ - Defaults to `3600` seconds

This is the length of time a reset password request is valid for in seconds 
after it has been created. 

#### `throttle_limit`

_Optional_ - Defaults to `3600` seconds

This is the length of time in seconds that must pass before a user can request a
 subsequent reset request. 

Setting this value _equal to or higher_ than `lifetime` will prevent a user from
 requesting a password reset before a previous reset attempt has either 1) Been 
 successfully completed. 2) The previous request has expired.

Setting this value _lower_ than `lifetime` will allow a user to make several 
reset password requests, even if any previous requests have _not_ been successfully
 completed or have not expired. This would allow for cases such as a user never 
 received the reset password request email.

#### `enable_garbage_collection`

_Optional_ - Defaults to `true`

Enable or disable the Reset Password Cleaner which handles expired reset password 
requests that may have been left in persistence.

## Advanced Usage

### Purging `ResetPasswordRequest` objects from persistence

The `ResetPasswordRequestRepositoryInterface::removeRequests()` method, which is 
implemented in the 
[ResetPasswordRequestRepositoryTrait](https://github.com/SymfonyCasts/reset-password-bundle/blob/main/src/Persistence/Repository/ResetPasswordRequestRepositoryTrait.php),
can be used to remove all request objects from persistence for a single user. This 
differs from the 
[garbage collection mechanism](https://github.com/SymfonyCasts/reset-password-bundle/blob/df64d82cca2ee371da5e8c03c227457069ae663e/src/Persistence/Repository/ResetPasswordRequestRepositoryTrait.php#L73)
which only removes _expired_ request objects for _all_ users automatically.

Typically, you'd call this method when you need to remove request object(s) for 
a user who changed their email address due to suspicious activity and potentially
has valid request objects in persistence with their "old" compromised email address.

```php
// ProfileController

#[Route(path: '/profile/{id}', name: 'app_update_profile', methods: ['GET', 'POST'])]
public function profile(Request $request, User $user, ResetPasswordRequestRepositoryInterface $repository): Response
{
    $originalEmail = $user->getEmail();

    $form = $this->createFormBuilder($user)
        ->add('email', EmailType::class)
        ->add('save', SubmitType::class, ['label' => 'Save Profile'])
        ->getForm()
    ;
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        if ($originalEmail !== $user->getEmail()) {
            // The user changed their email address.
            // Remove any old reset requests for the user.
            $repository->removeRequests($user);
        }
        
        // Persist the user object and redirect...
    }
    
    return $this->render('profile.html.twig', ['form' => $form]);
}
```

## Support

Feel free to open an issue for questions, problems, or suggestions with our bundle.
Issues pertaining to Symfony's Maker Bundle, specifically `make:reset-password`,
should be addressed in the [Symfony Maker repository](https://github.com/symfony/maker-bundle).

## API Usage Example

If you're using [API Platform](https://api-platform.com/), this example will
demonstrate how to implement ResetPasswordBundle into the API.

```php
// src/Entity/ResetPasswordRequest

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\ResetPasswordInput;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * @ORM\Entity(repositoryClass=ResetPasswordRequestRepository::class)
 */
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->id = new UuidV4();
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
```

Because the `ResetPasswordHelper::generateResetToken()` method is responsible for
creating and persisting a `ResetPasswordRequest` object after the reset token has been
generated, we can't call `POST /api/reset-passwords` with `['email' => 'someone@example.com']`.

We'll create 2 Data Transfer Objects (`DTO`) ~that will be used by a Data Persister
to generate the actual `ResetPasswordRequest` object from the email address provided
in the `POST` api call.~

```php
<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     output=false,
 *     collectionOperations={},
 *     itemOperations={"put"},
 *     shortName="reset-password"
 * )
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordInput
{
    /**
     * @ApiProperty(identifier=true, writable=false)
     */
    public string $token;

    /**
     * @Assert\NotBlank()
     */
    public string $plainTextPassword;
}
```

```php
<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     output=false,
 *     collectionOperations={
 *          "post" = {
 *              "status" = 202,
 *          },
 *     },
 *     itemOperations={},
 *     shortName="reset-password-request"
 * )
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordRequestInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ApiProperty(identifier=true)
     */
    public string $email;
}
```

```php
<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\ResetPasswordInput;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return ResetPasswordInput::class === $resourceClass && 'put' === $operationName;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): User
    {
        if (!is_string($id)) {
            throw new NotFoundHttpException('Invalid token.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($id);
        } catch (ResetPasswordExceptionInterface $ex) {
            // Log exception id needed
            throw new NotFoundHttpException();
        }

        if (!$user instanceof User) {
            throw new NotFoundHttpException('Invalid token.');
        }

        $this->resetPasswordHelper->removeResetRequest($id);

        return $user;
    }
}
```

Finally we'll create a Data Persister that is responsible for using the
`ResetPasswordHelper::class` to generate a `ResetPasswordRequest` and email the
token to the user.

```php
<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\ResetPasswordInput;
use App\Dto\ResetPasswordRequestInput;
use App\Entity\User;
use App\Message\SendResetPasswordMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
class ResetPasswordDataPersister implements ContextAwareDataPersisterInterface
{
    private UserRepository $userRepository;
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private MessageBusInterface $messageBus;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserRepository $userRepository, ResetPasswordHelperInterface $resetPasswordHelper, MessageBusInterface $messageBus, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->messageBus = $messageBus;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data, array $context = []): bool
    {
        if (!($data instanceof ResetPasswordInput || $data instanceof ResetPasswordRequestInput)) {
            return false;
        }

        if (isset($context['collection_operation_name']) && 'post' === $context['collection_operation_name']) {
            return true;
        }

        if (isset($context['item_operation_name']) && 'put' === $context['item_operation_name']) {
            return true;
        }

        return false;
    }

    public function persist($data, array $context = []): void
    {
        if (isset($context['collection_operation_name']) && 'post' === $context['collection_operation_name']) {
            /** @var ResetPasswordRequestInput $data */
            $this->generateRequest($data->email);

            return;
        }

        if (isset($context['item_operation_name']) && 'put' === $context['item_operation_name']) {
            /** @var ResetPasswordInput $data */
            if (!$context['previous_data'] instanceof User) {
                return;
            }

            $this->changePassword($context['previous_data'], $data->plainTextPassword);
        }
    }

    public function remove($data, array $context = []): void
    {
        throw new \RuntimeException('Operation not supported.');
    }

    private function generateRequest(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            return;
        }

        $token = $this->resetPasswordHelper->generateResetToken($user);

        /** @psalm-suppress PossiblyNullArgument */
        $this->messageBus->dispatch(new SendResetPasswordMessage($user->getEmail(), $token));
    }

    private function changePassword(User $previousUser, string $plainTextPassword): void
    {
        $userId = $previousUser->getId();

        $user = $this->userRepository->find($userId);

        if (null === $user) {
            return;
        }

        $encoded = $this->userPasswordEncoder->encodePassword($user, $plainTextPassword);

        $this->userRepository->upgradePassword($user, $encoded);
    }
}
```

## Security Issues
For **security related vulnerabilities**, we ask that you send an email to 
`ryan [at] symfonycasts.com` instead of creating an issue. 

This will give us the opportunity to address the issue without exposing the
vulnerability before a fix can be published.
