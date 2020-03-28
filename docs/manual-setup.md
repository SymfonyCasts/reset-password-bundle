# Manual Setup Guide

While we strongly encourage you to use the maker command for this bundle, if your use case does not permit you to do so, this guide will outline how your app should interact with the bundle.

The bundle provides a [helper](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/ResetPasswordHelper.php) that makes it easy to leverage the bundle to suite your needs. This guide assumes your app will be using this helper. Implementing a customized helper using the [`ResetPasswordHelperInterface`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/ResetPasswordHelperInterface.php) is beyond the scope of this documentation.

## Overview

The bundle has 2 primary responsibilities:
 - Create a unique, secure, hash for a user that's stored in persistence and provide a non-hashed token to retrieve the stored hash.
 
 - Validate the hash stored in persistence against a user provided token.

The reset password bundle handles these 2 responsibilities very well with a little help from the application using the bundle.

## Creating Tokens

Before a user can create a reset password request, the app must have an entity that implements [`ResetPasswordRequestInterface`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/Model/ResetPasswordRequestInterface.php). This entity will be used by the bundle to store a reset request in persistence. The entity must have a repository that implements [`ResetPasswordRequestRepositoryInterface`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/Persistence/ResetPasswordRequestRepositoryInterface.php). The repository is responsible for the actual creation of the request object and manipulating it  in persistence. 

The bundle provides traits for each of these objects that make it easy to implement the interfaces. Once the above objects have been created in the app, you must set the fully qualified repository class name in the bundle's [configuration](https://github.com/SymfonyCasts/reset-password-bundle/wiki/Confguration-Reference) file.

[`ResetPasswordHelper::generateResetToken()`](https://github.com/SymfonyCasts/reset-password-bundle/blob/239266e8ba6b513c053c86ac51feee9adc4e075c/src/ResetPasswordHelper.php#L63) requires you to provide a user object that will be used to identify the user requesting a reset. After the helper creates a request object and stores it in persistence, a [`ResetPassowrdToken`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/Model/ResetPasswordToken.php) object is returned. The token object contains the token which should be provided to the user for validation later on.

## Verifying Tokens

After a user has received their reset token and is ready to reset their password, the app should call the [`ResetPasswordHelper::validateTokenAndFetchUser()`](https://github.com/SymfonyCasts/reset-password-bundle/blob/239266e8ba6b513c053c86ac51feee9adc4e075c/src/ResetPasswordHelper.php#L97) method with the token provided by the user. Internally, this method will fetch the [`ResetPasswordRequestInterface`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/Model/ResetPasswordRequestInterface.php) object from persistence, verify that the token provided by the user matches the hash stored in the request object, and then return the user object back to the app.

## Failures
 
If the helper is unable to generate or validate a reset token, or remove a reset request from persistence, a [`ResetPasswordExceptionInterface`](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/src/Exception/ResetPasswordExceptionInterface.php) object is thrown by the helper. This exception should be captured by the app and handled accordingly.