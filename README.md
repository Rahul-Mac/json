# json

`json` is a fluent, object-oriented wrapper around PHP’s native JSON functions.

# Installation

```bash
composer require rahulmac/json
```

# Prerequisites

- PHP v8.0+

# Usage

## Encoding JSON

Use `Json::from()` to encode PHP values into JSON.

```php
use Rahul\Json\Json;

$json = Json::from(['foo' => 'bar'])->stringify();
```

To pretty-print:

```php
$json = Json::from(['foo' => 'bar'])->prettify();
```

Using custom flags and depth:

```php
$json = Json::from($data)
    ->withFlags(JSON_UNESCAPED_SLASHES)
    ->withDepth(256)
    ->stringify();
```

## Decoding JSON

Use `Json::of()` to decode a JSON string.

```php
use Rahul\Json\Json;

$value = Json::of('{"foo":"bar"}')->parse();
```

To decode as an array:

```php
$array = Json::of('{"foo":"bar"}')->toArray();
```

To decode as an object:

```php
$object = Json::of('{"foo":"bar"}')->toObject();
```

To validate:

```php
$isValid = Json::of('{"foo":"bar"}')->isValid(); // true
$isValid = Json::of('{invalid json}')->isValid(); // false
```

> [!NOTE]
> While encoding/decoding use `addFlags()` to append flags and `withFlags()` to override them.

> [!WARNING]
> All encoding/decoding methods throw `\JsonException`

# License

`json` is open-sourced software licensed under the [MIT license](LICENSE).
