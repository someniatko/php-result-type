# someniatko/result-type

Library representing a generic Result type with Success and Error states,
made for aiding this functional programming pattern in PHP.

It is generically typed using Psalm annotations, so you are expected to use Psalm if
you want typechecking of your code.

**Why using this library if `graham-campbell/result-type` exists?**  
Unfortunately, there are several downsides of that library:
- while it's also typed with Psalm annotations, actually using and checking against them might be painful.
- it's coupled to the `phpoption/phpoption` library which unfortunately suffers from the same problem.
- there is no convenience method in Result interface which would allow getting either value from it.


Works best with [`someniatko/result-type-psalm-plugin`][psalm-plugin]!


## Installation
This library requires PHP 8.2, 8.3 or 8.4.
You can install it via Composer:

```shell
composer install someniatko/result-type
```


## Usage

See [`ResultInterface`](src/ResultInterface.php) for details.

Example:

```php
<?php

use Someniatko\ResultType\Success;
use Someniatko\ResultType\Error;

$value = (new Success('Let it be'))
    ->map(fn (string $s) => substr_count($s, ' ')) // Success<2>
    ->chain(fn (int $wordsCount) => $wordsCount > 3 
        ? new Success('Long text') 
        : new Error('short text')
    ) // Error<'short text'>
    ->map(fn (string $s) => str_replace(' ', '', $s)) // will not be called because we're in Error result
    ->mapError(fn (string $s) => strtoupper($s)) // Error<'SHORT TEXT'>
    ->get(); // 'SHORT TEXT'
```



[psalm-plugin]: https://packagist.org/packages/someniatko/result-type-psalm-plugin
