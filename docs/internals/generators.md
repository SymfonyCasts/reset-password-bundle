# An in-depth look at the generators used in the bundle

## Random Generator

The `ResetPasswordRandomGenerator::class` is intended to generate random data
 needed by internal bundle logic. Currently the class only provides one method, 
 `getRandomAlphaNumStr()`.

### `getRandomAlphaNumStr(int $length)`

_Generate a random alpha numeric string of desired length._

Borrowed from Laravel's `Str::random()`

`$length` - desired length of string returned

## Token Generator

`SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator::class`

_An internal, non-extendable, generator used to create cryptographically secure
one-way tokens._

Requires a secure string used to sign tokens & a random string
 generator (`ResetPasswordRandomGenerator`). Provides one method, `getToken
 ()`, that returns a `ResetPasswordTokenComponents` object.
 
Tokens are generated via PHP's `hash_hmac()` method. Using the `sha256
` algorithm & a secure signing key, a JSON encoded string is encrypted one-way
 for use as a token to validate password reset requests. The JSON encoded
 data string is comprised of:
 
 - a verifier string
 - user identifier
 - timestamp