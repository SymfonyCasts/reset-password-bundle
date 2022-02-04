# ResetPasswordBundle: Mind-Blowing (and Secure) Password Resetting for Symfony

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
    request_password_repository: App\Repository\PasswordResetRequestRepository
    lifetime: 3600
    throttle_limit: 3600
    enable_garbage_collection: true
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

## Support

Feel free to open an issue for questions, problems, or suggestions with our bundle.
Issues pertaining to Symfony's Maker Bundle, specifically `make:reset-password`,
should be addressed in the [Symfony Maker repository](https://github.com/symfony/maker-bundle).

## Security Issues
For **security related vulnerabilities**, we ask that you send an email to 
`ryan [at] symfonycasts.com` instead of creating an issue. 

This will give us the opportunity to address the issue without exposing the
vulnerability before a fix can be published.
