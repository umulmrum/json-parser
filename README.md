JSON Parser
===========

JSON Parser is a streaming JSON parsing library.

[![Latest Stable Version](https://poser.pugx.org/umulmrum/json-parser/version)](https://packagist.org/packages/umulmrum/json-parser)
[![Latest Unstable Version](https://poser.pugx.org/umulmrum/json-parser/v/unstable)](https://packagist.org/packages/umulmrum/json-parser)
[![License](https://poser.pugx.org/umulmrum/json-parser/license)](https://packagist.org/packages/umulmrum/json-parser)
[![Build Status](https://travis-ci.org/umulmrum/json-parser.svg?branch=master)](https://travis-ci.org/umulmrum/json-parser)

Requirements
------------

- PHP >= 7.1

That's it really.

Installation
------------

Install the library using Composer.

```
composer require umulmrum/json-parser
```

Status
------

This library is in alpha state. I think the results are correct, but will not
consider it beta until the following requirements are met:

- 95+% code coverage in unit tests.
- Useful documentation.
- Improved performance.

Release 1.0.0 will be released after beta release and when the code was
production proofed.

Usage
-----

TODO (sorry).

Performance
-----------

The PHP function json_decode() is about two orders of magnitude faster, but will
not be able to parse huge JSON files without excessive memory usage. This library
is able to parse JSON in a streamed way (returning one first-level element at a
time), so that memory usage is constantly low, independent of data size.

I hope to be able to improve performance - feel free to contribute!

Until then, you might want to use json_decode for smaller data sets.

Contribution
------------

Contributions are highly welcome. Please follow these rules when submitting a PR:

- mimic existing code for style and structure
- add unit tests for all of your code
- use the Symfony code style (php-cs-fixer with symfony level)

By submitting a PR you agree that all your contributed code may be used under the MIT license.

License
-------

This library is licensed under the MIT License. See [LICENSE](LICENSE) for details.
