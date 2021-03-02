# Changelog

*We intend to follow [Semantic Versioning 2.0.0](https://semver.org/), if you 
find a change that break's semver, please create an issue.*

## NEXT

## 1.4.0

- [#145](https://github.com/SymfonyCasts/reset-password-bundle/pull/145) Add German translations
- [#148](https://github.com/SymfonyCasts/reset-password-bundle/pull/148) Add French translations
- [#149](https://github.com/SymfonyCasts/reset-password-bundle/pull/149) Add Polish translations
- [#150](https://github.com/SymfonyCasts/reset-password-bundle/pull/145) Add Serbian translations
- [#151](https://github.com/SymfonyCasts/reset-password-bundle/pull/151) Add Ukrainian translation
- [#152](https://github.com/SymfonyCasts/reset-password-bundle/pull/152) Add Russian translation
- [#157](https://github.com/SymfonyCasts/reset-password-bundle/pull/157) Add Spanish translation

## v1.3.0

- [#143](https://github.com/SymfonyCasts/reset-password-bundle/pull/143) Adds controller trait methods to set/get the 
  `ResetPasswordToken::class` object in the session. The following `ResetPasswordControllerTrait::class` methods have been deprecated: 
  `setCanCheckEmailInSession()`, `canCheckEmail()`

## v1.2.2

- [#139](https://github.com/SymfonyCasts/reset-password-bundle/pull/139) Fixed regression
  in 1.2.2 with expiration DateTime timezone

## v1.2.1

- [#135](https://github.com/SymfonyCasts/reset-password-bundle/pull/135) Add translation support for signature expiration time
- [#135](https://github.com/SymfonyCasts/reset-password-bundle/pull/134) Fixed invalid signature expiration time

## v1.2.0

*Dec 10th, 2020*

- [#134](https://github.com/SymfonyCasts/reset-password-bundle/pull/134)  - Allow the bundle to be used with PHP 8 - thanks to @ker0x

## v1.1.0

*April 17th, 2020*

- [#104](https://github.com/SymfonyCasts/reset-password-bundle/pull/104) - [feature] add additional detail to TooManyPasswordRequestsException
- [#103](https://github.com/SymfonyCasts/reset-password-bundle/pull/103) - [bug] increase time before expired requests are garbage collected to 1 week
- [#99](https://github.com/SymfonyCasts/reset-password-bundle/pull/99) - Fix typo hasUserHisThrottling to hasUserHitThrottling
- [#97](https://github.com/SymfonyCasts/reset-password-bundle/pull/97) - Clarify that the repository trait is for Doctrine ORM only

- Various other minor internal improvements

## v1.0.0

*April 5th, 2020*

- [#93 - fixed remove-expired CLI command error](https://github.com/SymfonyCasts/reset-password-bundle/pull/93)

## v1.0.0-BETA2

*April 3rd, 2020*

- [#79 - Fixed incorrect fake repository namespace in service definition](https://github.com/SymfonyCasts/reset-password-bundle/pull/79)

## v1.0.0-BETA1

*March 27th, 2020*

- Initial pre-release for testing
