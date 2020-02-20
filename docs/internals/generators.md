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
 
Tokens are generated via PHP's `hash_hmac()` method using the `sha256
` algorithm & a secure signing key. A JSON encoded string is passed as the
 data argument and ultimately encrypted. The JSON encoded data string is comprised of:
 
 - a verifier string
 - user identifier
 - timestamp
 
 The hashed string returned by `hash_hmac()` is the token which is used to
 validate password reset requests.
  
 As the token is encrypted one-way, to verify that a token is valid, another
 token must be generated and then compared against the original token. To
 facilitate validation, `getToken()` accepts an optional 3rd argument, `(string) $verifier`,
 which allows a token to be re-created. 