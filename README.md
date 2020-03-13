# ResetPasswordBundle

Worrying about how to deal with users that can't remember their password? We've got you covered! This bundle provides a secure out of the box solution to allow users to reset their forgotten passwords.

## Installation

@TODO - To be added upon release

The bundle can be installed using Composer or the [Symfony binary](https://symfony.com/download):

```
composer install X/X
```
or 
```
symfony composer install x/x
```

## Usage

There are two ways to get started, the easiest and preferred way is to use Symfony's [MakerBundle](https://github.com/symfony/maker-bundle). The Maker will take care of everything from creating configuration, to generating your templates, controllers, and entities.

### Using Symfony's Maker Bundle (Recommended)

- Run `bin/console make:reset-password`, answer a couple questions, enjoy our bundle!

### Setting things up manually

If you prefer to take care of the leg work yourself, checkout the _NAME_ guide. We still recommend using the Maker command to get a feel for how we intended the bundle to be used.

---

If you used our Symfony Maker command `bin/console make:reset-password` after installation, your app is ready to go. Go to `https://your-apps-domain/reset-password`, fill out the form, click on the link sent to your email, and change your password. That's it! The Reset Password Bundle takes care of the rest.

The above assumes you have already setup [authentication](https://symfony.com/doc/current/security.html) with a registered user account & configured Symfony's [mailer](https://symfony.com/doc/current/mailer.html) in your app.

## Configuration

You can change the default configuration parameters for the bundle in the `config/packages/reset_password.yaml` config file created by Maker. For details on what can be configured, checkout the [configuration reference](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/docs/configuration-reference.md).