# Cut/Copy & Paste Laravel Eloquent models into another table

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elipzis/laravel-pastable-model/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elipzis/laravel-pastable-model/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elipzis/laravel-pastable-model.svg?style=flat-square)](https://packagist.org/packages/elipzis/laravel-pastable-model)

Enable your models to regularly cut/copy & paste their data into another table.

- Cut & Paste or Copy & Paste
- Scheduled Jobs available to regularly & asynchronously run
- Cut & Paste in chunks, to split potential long-running processes
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
    //The default cut&paste chunk size (limit)
    'chunkSize' => 1000,
    //Auto-create tables, if not existing
    'autoCreate' => false,
    //Enable detailed logging to any accepted and configured level
    'logging' => [
        'enabled' => false,
        'level' => null,
    ],
];
```

## Usage

To make your model copy & pastable, add the trait `CopyPastable`.
It will copy & paste your configured query result into the target table.

```php
...
use ElipZis\Pastable\Models\Traits\CopyPastable;
...

class YourModel extends Model {

    use CopyPastable;    
    ...
```

To make your model cut & pastable, add the trait `CutPastable`.
It will cut (delete) & paste your configured query result into the target table.
If more rows than the chunk size (limit) are affected, it will respawn its own job until completed.

```php
...
use ElipZis\Pastable\Models\Traits\CutPastable;
...

class YourModel extends Model {

    use CutPastable;    
    ...
```

### Configuration

To use any trait, you need to configure two settings:

- the target table
- the query to read its data from

#### Target table _(mandatory)_

You must define the target table name.

```php
...

class YourModel extends Model {

    ...
    protected string $pastableTable = 'log_something';    
    ...
```

or by overriding the getter function, to e.g. create dynamic table names

```php
...

class YourModel extends Model {

    ...
    public function getPastableTable(): string
    {
        return 'log_something_' . Carbon::now()->format('Y_m_d');
    }
    ...
```

If the table does not exist, you can use the configuration setting `autoCreate` and set it to `true` to have the system
try to create the table from your query source.

**It is recommended for you to create the table manually or via migration, as the automation is not fully tested and
functional to any database system and table structure.**

#### Query _(mandatory)_

You must define the query to use to read data and cut/copy & paste into the target table.

```php
...

class YourModel extends Model {

    ...
    public function getPastableQuery(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subDay());
    }
    ...
```

You can use any query that returns a `Builder` object.

In the case of cut & paste, the default `chunkSize` is used as a limiter. You can set your own limit by
adding `->limit()` to the query or override the configuration setting in general.

#### Connection _(optional)_

You can give a separate connection if you want the target table to be generated and filled in e.g. another database.

```php
...

class YourModel extends Model {

    ...
    protected string $pastableConnection = 'logging';    
    ...
```

### Run

After implementation and configuration, you got three options to trigger the cut/copy & paste jobs:

- Manually dispatching the jobs
- Scheduled dispatch
- Running a command to trigger it manually

#### Scheduled

The preferred way is to run the job on a schedule, configured via the Kernel, e.g. daily:

```php
namespace App\Console;

...
use ElipZis\Pastable\Jobs\PastableJob;
...

class Kernel extends ConsoleKernel

    ...
    protected function schedule(Schedule $schedule)
    {
        ...
        $schedule->job(PastableJob::class)->daily();
        ...
    }
    ...
```

#### Via Command

You may also trigger the execution manually by using the command(s):

- All cut/copy & pastable model classes: `php artisan pastable:all`
- Only copy & pastable model classes: `php artisan pastable:copy`
- Only cut & pastable model classes: `php artisan pastable:cut`

#### Manual dispatch

The final option is to trigger the job manually inside any of your functions, any logic, any application code:

```php
...
use ElipZis\Pastable\Jobs\PastableJob;
...

class YourClass

    ...
    protected function yourFunction()
    {
        ...
        PastableJob::dispatch();
        ...
    }
    ...
```

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
