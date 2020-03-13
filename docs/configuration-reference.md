# Complete configuration example

```
symfonycasts_reset_password:
  request_password_repository: App\Repository\PasswordResetRequestRepository
  lifetime: 3600
  throttle_limit: 3600
  enable_garbage_collection: true
```

## Parameters:

### `request_password_repository`

_Required_

The complete namespace of the repository for the ResetPasswordRequest entity. If you used `make:reset-password`, this will be `App\Repository\ResetPasswordRequestRepository`.

### `lifetime`

_Optional_ - Defaults to `3600` seconds

This is the length of time a reset password request is valid for in seconds after it has been created. 

### `throttle_limit`

_Optional_ - Defaults to `3600` seconds

This is the length of time in seconds that must pass before a user can request a subsequent reset request. 

Setting this value _equal to or higher_ than `lifetime` will prevent a user from requesting a password reset before a previous reset attempt has either 1) Been successfully completed. 2) The previous request has expired.

Setting this value _lower_ than `lifetime` will allow a user to make several reset password requests, even if any previous requests have _not_ been successfully completed or have not expired. This would allow for cases such as a user never received the reset password request email.

### `enable_garbage_collection`

_Optional_ - Defaults to `true`

Enable or disable the Reset Password Cleaner which handles expired reset password requests that may have been left in persistence.
