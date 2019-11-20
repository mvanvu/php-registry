# php-registry

``` php
use MaiVu\Php\Registry;

$registry = new Registry;

// Set a value
$registry->set('foo', 'bar');

// Get a value
$value = $registry->get('foo');

// Set a deep value
$registry->set('foo.bar', 'deep');

// Get a deep value
$deep = $registry->get('foo.bar'); // Return 'deep' string
$foo  = $registry->get('foo'); // Return array ['bar' => 'deep']

// Check exists
$registry->has('foo');

// or
$registry->has('foo.bar');

```

## Initialise data

``` php
use MaiVu\Php\Registry;

$registry = new Registry($data);

$data is an array
$data = ['foo' => 'bar'];

or is an object
$data = new stdClass;

or is an instance of Registry
$data = new Registry;

or is a json string
$data = '{"foo": "bar"}';

or is php file that must return an array
$data = 'path/to/file/data.php';

or is json file
$data = 'path/to/file/data.json';

```

#### Merge another Registry

``` php
use MaiVu\Php\Registry;

// Merge an instance
$registry1 = new Registry(['foo' => 'bar']);
$registry2 = new Registry(['foo2' => 'bar2']);
$registry1->merge($registry2);

// Merge a mixed data
$registry1->merge(['foo2' => 'bar2']);
$registry1->merge(new stdClass);
$registry1->merge('{"foo": "bar"}');
$registry1->merge('path/to/file/data.php');
$registry1->merge('path/to/file/data.json');
```
#### Parse data to array

``` php
use MaiVu\Php\Registry;
$arrayData = Registry::parseData('{"foo": "bar"}');

// Data is mixed type
$arrayData = Registry::parseData('path/to/file/data.php'); // Faster
$arrayData = Registry::parseData('path/to/file/data.json');

```

#### Dump data

``` php
use MaiVu\Php\Registry;
$registry = new Registry(['foo' => 'bar']);

// To array
var_dump($registry->toArray());

// To json string
var_dump($registry->toString());

```

## Installation via Composer

```json
{
	"require": {
		"mvanvu/php-registry": "~1.0"
	}
}
```

Alternatively, from the command line:

```sh
composer require mvanvu/php-registry
```