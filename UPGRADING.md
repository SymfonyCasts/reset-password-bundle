# Upgrade from 1.x to 2.0

ResetPasswordBundle now requires PHP `8.3`+ & Symfony `6.4.5`+

## ResetPasswordHelper

- Class became `@final` in `v1.22.0` and in `v2.0.0` the `@final` annotation was 
replaced with the `final` class keyword. Extending this class is not allowed.

## ResetPasswordRemoveExpiredCommand

- Class became `@final` in `v1.22.0` and in `v2.0.0` the `@final` annotation was
    replaced with the `final` class keyword. Extending this class is not allowed.
