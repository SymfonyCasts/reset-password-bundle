# Upgrade from 1.x to 2.0

ResetPasswordBundle now requires PHP `8.3`+ & Symfony `6.4.5`+

## ResetPasswordHelper

- Class became `@final` in `v1.22.0`. Extending this class will not be allowed
  in version `v2.0.0`.

## ResetPasswordRemoveExpiredCommand

- Class became `@final` in `v1.22.0`. Extending this class will not be allowed
  in version `v2.0.0`.
