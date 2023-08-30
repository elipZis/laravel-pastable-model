# Cut/Copy & Paste Laravel Eloquent model data regularly into other tables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require elipzis/laravel-pastable-model
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-pastable-model-config"
```

This is the contents of the published config file:

```php
return [
    //The default chunk size (limit)
    'chunkSize' => 1000,
    //Auto-create tables, if not existing
    'autoCreate' => false,
    //Do you need logging?
    'logging' => [
        'enabled' => false,
        'level' => null,
    ],
];
```

## Usage

Make your model copy & pastable by adding the trait:

```php
...
use ElipZis\Pastable\Models\Traits\CopyPastable;
...

class YourModel extends Model {

    use CopyPastable;    
    ... 
    protected $pastableTable = 'log_model';
    ...
```

`CopyPastable` will copy and paste the queried data to the defined table.

Make your model cut & pastable by adding the trait:

```php
...
use ElipZis\Pastable\Models\Traits\CutPastable;
...

class YourModel extends Model {

    use CutPastable;    
    ... 
    protected $pastableTable = 'log_model';
    ...
```

`CutPastable` will cut (delete) and paste the queried data to the defined table.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [elipZis GmbH](https://github.com/elipZis)
- [NeA](https://github.com/nea)
- [All Contributors](https://github.com/elipZis/laravel-pastable-model/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
