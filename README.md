JSON Parser
===========

JSON Parser is a streaming JSON parsing library. It returns the same results as 
`\json_decode('some-string', true, 512, JSON_UNESCAPED_SLASHES)`.

You might want to use this library when decoding JSON that is multiple MiB in size, 
where the in-memory approach of `\json_decode()` consumes excessive amounts of memory.
This library has constant memory usage independent of the size of the JSON string if
used correctly (which is not difficult, see below).

You might NOT want to use this library for small JSON strings of less than ~1 MiB, as
`\json_decode()` is about 50x faster. While I aim for performance improvements, the C
implementation of `\json_decode()` will always be faster than this PHP implementation.

[![Latest Stable Version](https://poser.pugx.org/umulmrum/json-parser/version)](https://packagist.org/packages/umulmrum/json-parser)
[![Latest Unstable Version](https://poser.pugx.org/umulmrum/json-parser/v/unstable)](https://packagist.org/packages/umulmrum/json-parser)
[![License](https://poser.pugx.org/umulmrum/json-parser/license)](https://packagist.org/packages/umulmrum/json-parser)
[![Build Status](https://travis-ci.org/umulmrum/json-parser.svg?branch=master)](https://travis-ci.org/umulmrum/json-parser)

Requirements
------------

- PHP >= 7.1

That's it really. However, using the mb_string extension will improve performance.

Installation
------------

Install the library using Composer.

```
composer require umulmrum/json-parser
```

Status
------

This library is in beta state. Code coverage in unit tests is high and the library is already used in 
production, but it is not yet considered stable; you are encouraged to test it and report issues. 

Usage
-----

```php
<?php

include __DIR__.'/vendor/autoload.php';

$parser = umulmrum\JsonParser\JsonParser::fromFile('/path/to/file.json');
foreach ($parser->generate() as $value) {
    var_dump($value);
}
```

Every valid JSON string consists of either a root array or a root object containing values.
The `generate()` method returns these first-level values, one at a time. 
Each returned value is a PHP array containing a single key/value pair; the key is 
- the key of the first-level value if the root element is an object.
- the index of the first-level value if the root element is an array.

These values can then be consumed (e.g. persisted, printed or sent by AMQP) and be discarded afterwards.

If the JSON string consists of an empty object or array, an empty array is returned.

If the JSON string is empty or consists only of whitespace, null is returned.

On errors, one of these exceptions is thrown:
 - `\umulmrum\JsonParser\InvalidJsonException` if invalid JSON is encountered. The exception provides information
   on the line and column of the JSON string in which the error occurred (sometimes this information is off by
   one line or column).
-  `\umulmrum\JsonParser\DataSource\DataSourceException` if the source file could not be read for any reason.

If no exception was thrown, the result can be "trusted" (i.e. there is no need to check for errors afterwards
such as `\json_last_error()`).

Alternative Usage
-----------------

Alternatively, instantiate the parser from a JSON string:

```php
$parser = umulmrum\JsonParser\JsonParser::fromString('["test"]');
```

Be aware that this will not be very memory-efficient as PHP uses a lot of memory for strings. Whenever
possible, the `fromFile()` factory method is recommended.

JsonParser can also return the complete content by calling `all()` (which is again not recommended
because of memory).

Customization
-------------

Internally, JsonParser uses an implementation of `\umulmrum\JsonParser\DataSource\DataSourceInterface` to receive
the characters to parse. The factory methods for JsonParser are simple convenience methods that handle instantiating 
the data source, but the constructor can be called manually, passing a data source, so that custom data
source implementations can be used. See the docblocks of `\umulmrum\JsonParser\DataSource\DataSourceInterface` for
details. You might wish to extend `\umulmrum\JsonParser\DataSource\AbstractDataSource` that contains some convenience
methods.

The example from the previous section can be written as follows:

```php
$parser = new umulmrum\JsonParser\JsonParser(new \umulmrum\JsonParser\DataSource\FileDataSource('["test"]'));
```

Backwards Compatibility
-----------------------

This library follows Semantic Versioning and will therefore only introduce breaking changes in the public
API in major versions (and in minor versions before reaching 1.0.0). The public API consists of:
- `\umulmrum\JsonParser\JsonParser` (public methods). 
- `\umulmrum\JsonParser\DataSource\DataSourceInterface` (public methods).
- `\umulmrum\JsonParser\DataSource\AbstractDataSource` (public and protected methods).

All other classes and methods are considered internal and can change anytime.

Contribution
------------

Contributions are highly welcome. Please follow these rules when submitting a PR:

- Mimic existing code for style and structure.
- Add unit tests for all of your code.
- Use the Symfony code style (php-cs-fixer with the `.php_cs.dist` ruleset in this repository).

By submitting a PR you agree that all your contributed code may be used under the MIT license.

License
-------

This library is licensed under the MIT License. See [LICENSE](LICENSE) for details.
