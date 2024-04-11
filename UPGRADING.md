# Upgrade from 1.x to 2.0

ResetPasswordBundle now requires PHP `8.3`+ & Symfony `6.4.5`+

## ResetPasswordHelperInterface

- The `$resetRequestLifetime` argument for `generateResetToken()` must exist in 
classes that implement the interface.

```diff
- public function generateResetToken(object $user/* , ?int $resetRequestLifetime = null */): ResetPasswordToken;
+ public function generateResetToken(object $user, ?int $resetRequestLifetime = null): ResetPasswordToken;
```

## ResetPasswordHelper

- Class became `@final` in `v1.22.0` and in `v2.0.0` the `@final` annotation was 
replaced with the `final` class keyword. Extending this class is not allowed.

## ResetPasswordRemoveExpiredCommand

- Class became `@final` in `v1.22.0` and in `v2.0.0` the `@final` annotation was
    replaced with the `final` class keyword. Extending this class is not allowed.

- Command is now registered using the Symfony `#[AsCommand]` attribute.

## ResetPasswordControllerTrait

- Removed deprecated `setCanCheckEmailInSession()` method from trait.

- Removed deprecated `canCheckEmail()` method from trait.

## ResetPasswordRequestTrait

- Annotation support for ResetPasswordRequest Doctrine entities that use the
trait has been dropped - attribute mapping is required.

- Property types were added to `selector`, `hashedToken`, `requestedAt`, & `expiresAt`.

```diff
- protected $selector;
+ protected string $selector;

- protected $hashedToken;
+ protected string $hashedToken;

- protected $requestedAt;
+ protected \DateTimeImmutable $requestedAt;

- protected $expiresAt;
+ protected \DateTimeInterface $expiresAt;
```

- `initalize()` now returns `void`. Previously the return type was not declared

```diff
- protected function initialize(....)
+ protected function initialize(....): void
```
