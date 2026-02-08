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
use Rahulmac\Json\Json;

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
use Rahulmac\Json\Json;

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

### Accessors

Use `get()` with dot notation to access nested values:

```php
use Rahulmac\Json\Json;

$decoder = Json::of('{"user":{"id":123,"name":"Alice","active":true,"balance":99.5}}');

$id = $decoder->get('user.id');          // 123
$name = $decoder->get('user.name');      // "Alice"
$status = $decoder->get('user.active');  // true
$unknown = $decoder->get('user.unknown', 'default'); // "default"
```

For type-safety, use can use type-safe helpers that fetch a value and cast it to the requested type:

```php
$age     = $decoder->asInt('user.id');           // 123
$balance = $decoder->asFloat('user.balance');   // 99.5
$name    = $decoder->asString('user.name');     // "Alice"
$active  = $decoder->asBool('user.active');     // true

$roles   = $decoder->asArray('user.roles', []); // [] if missing
```

> [!NOTE]
> While encoding/decoding use `addFlags()` to append flags and `withFlags()` to override them.

> [!WARNING]
> All encoding/decoding/accessor methods throw `\JsonException` if an invalid JSON is received.

# License

`json` is open-sourced software licensed under the [MIT license](LICENSE).
