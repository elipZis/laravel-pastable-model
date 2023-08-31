# Cut/Copy & Paste Laravel Eloquent models into another table

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)

Enable your models to regularly cut/copy and paste data into another table.

- Cut & Paste or Copy & Paste
- Scheduled Job to regularly & asynchronously execute in chunks
- Store data e.g. into logging or daily tables and keep the production data clean

## Installation

You can install the package via composer:

```bash
composer require elipzis/laravel-pastable-model
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="pastable-model-config"
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

### Configuration

## Testing

```bash
composer test
```

## Notes

This package is heavily inspired by two incredible resources:

- [Laravel Prunable](https://laravel.com/docs/10.x/eloquent#pruning-models)
- [Flare's cleaning big tables](https://flareapp.io/blog/7-how-to-safely-delete-records-in-massive-tables-on-aws-using-laravel)

Kudos and Thanks to both for the inspiration.

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
