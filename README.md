# Laravel Macro Describer
Automatically generate IDE autocompletion helpers for Laravel macros/mixins.

[![Latest Version](https://img.shields.io/github/release/claudiodekker/laravel-macro-describer.svg?style=flat-square)](https://github.com/claudiodekker/laravel-macro-describer/releases)
[![Build Status](https://img.shields.io/travis/claudiodekker/laravel-macro-describer/master.svg?style=flat-square)](https://travis-ci.org/claudiodekker/laravel-macro-describer)
[![Quality Score](https://img.shields.io/scrutinizer/g/claudiodekker/laravel-macro-describer.svg?style=flat-square)](https://scrutinizer-ci.com/g/claudiodekker/laravel-macro-describer)
[![StyleCI](https://styleci.io/repos/292818399/shield)](https://styleci.io/repos/292818399)
[![Total Downloads](https://img.shields.io/packagist/dt/claudiodekker/laravel-macro-describer.svg?style=flat-square)](https://packagist.org/packages/claudiodekker/laravel-macro-describer)

## Installation

You can install the package via composer:

```bash
composer require claudiodekker/laravel-macro-describer
```

## Usage

Simply run `php artisan macro:generate-helpers`, or append it to your `composer.json`'s `post-autoload-dump` section like this:

```json
"scripts": {
    "post-autoload-dump": [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan package:discover --ansi",
        "@php artisan macro:generate-helpers"
    ],
```

When ran, the script does the following:

- Find all classes that use the `Macroable` trait.
- Fetch all registered macros/mixins using Reflection.
- Parse all method details using reflection (name, parameters & types, return type etc.)
- Generate an [PHPDocumentor-compatible `_ide_helpers.php` file](https://manual.phpdoc.org/HTMLSmartyConverter/PHP/phpDocumentor/tutorial_tags.method.pkg.html)

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email claudio@ubient.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
