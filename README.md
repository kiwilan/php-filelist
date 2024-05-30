# PHP FileList

![Banner with eReader picture in background and PHP eBook title](https://raw.githubusercontent.com/kiwilan/php-ebook/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package for recursive file listing, exportable in JSON format.

> [!NOTE]
> The aim of this package is to provide a simple way to list files in a directory, with options to customize the scan. But the real feature is usage of custom binaries, if you want to add an interesting binary, you can open an issue or a pull request.

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-filelist
```

## Usage

```php
$list = FileList::make('/path/to/scan')->run();

$list->getFiles(); // List of files as `string[]`
$list->getErrors(); // List of errors as `string[]|null`
$list->getTimeElapsed(); // Time elapsed in seconds as `float`
$list->getTotal(); // Total files as `int`
$list->isSuccess(); // Success status as `bool`
```

### Options

Show hidden files, default is `false`.

```php
$list = FileList::make('/path/to/scan')->run();
```

Save as JSON.

```php
$list = FileList::make('/path/to/scan')->saveAsJson('/path/to/json')->run();
```

Throw exception on error, otherwise errors are stored in the list.

```php
$list = FileList::make('/path/to/scan')->throwOnError()->run();
```

Limit the number of files to scan.

```php
$list = FileList::make('/path/to/scan')->limit(100)->run();
```

Skip extensions, case insensitive.

```php
$list = FileList::make('/path/to/scan')->skipExtensions(['txt', 'md'])->run();
```

Disable recursive scan.

```php
$list = FileList::make('/path/to/scan')->notRecursive()->run();
```

Disable PHP memory limit.

```php
$list = FileList::make('/path/to/scan')->noMemoryLimit()->run();
```

### Use custom binaries

If you want to add a new binary, you can open an issue or a pull request.

#### `find`

The `find` binary is used to list files in a directory, you can add path of binary as parameter of `withFind()` method if it's not in your PATH.

```php
$list = FileList::make('/path/to/scan')->withFind()->run();
```

### `scout`

The `scout` binary is used to list files in a directory, you can add path of binary as parameter of `withScout()` method if it's not in your PATH.

> [!NOTE] > `scout` is a Rust CLI tool built to list files, you can find the source code [here](https://github.com/ewilan-riviere/scout).

```php
$list = FileList::make('/path/to/scan')->withScout()->run();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

-   [Ewilan Rivière](https://github.com/kiwilan)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
