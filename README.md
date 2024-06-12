# PHP FileList

![Banner with rocks and PHP FileList title](https://raw.githubusercontent.com/kiwilan/php-filelist/main/docs/banner.jpg)

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
$list->getSplFiles(); // List of SplFileInfo as `SplFileInfo[]`
$list->getErrors(); // List of errors as `string[]|null`
$list->getTimeElapsed(); // Time elapsed in seconds as `float`
$list->getTotal(); // Total files as `int`
$list->isSuccess(); // Success status as `bool`
```

### Options

Show hidden files, default is `false`.

```php
$list = FileList::make('/path/to/scan')->showHidden()->run();
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

Skip filenames.

```php
$list = FileList::make('/path/to/scan')->skipFilenames(['file.txt', 'README.md'])->run();
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

#### `scout`

The `scout` binary is used to list files in a directory, you can add path of binary as parameter of `withScout()` method if it's not in your PATH.

> [!NOTE]
> Binary `scout` is a Rust CLI tool built to list files, you can find the source code [here](https://github.com/ewilan-riviere/scout).

> [!IMPORTANT]
> Minimum `scout` version is `0.2.0`.

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

[<img src="https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg" height="120rem" width="100%" />](https://github.com/kiwilan)

[version-src]: https://img.shields.io/packagist/v/kiwilan/php-filelist.svg?style=flat&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/php-filelist
[php-version-src]: https://img.shields.io/static/v1?style=flat&label=PHP&message=v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/php-filelist.svg?style=flat&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/php-filelist
[license-src]: https://img.shields.io/github/license/kiwilan/php-filelist.svg?style=flat&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/php-filelist/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/php-filelist/run-tests.yml?branch=main&label=tests&style=flat&colorA=18181B
[tests-href]: https://packagist.org/packages/kiwilan/php-filelist
[codecov-src]: https://codecov.io/gh/kiwilan/php-filelist/graph/badge.svg?token=LhW38C1VKZ
[codecov-href]: https://codecov.io/gh/kiwilan/php-filelist
