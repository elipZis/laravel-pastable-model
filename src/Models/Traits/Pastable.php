<?php

namespace ElipZis\Pastable\Models\Traits;

use ElipZis\Pastable\Helper\PastableLogger;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LogicException;
use Throwable;

trait Pastable
{
    use PastableLogger;

    /**
     * @throws Exception
     */
    protected function preparePastable(): Builder
    {
        //Set a limit, if no other limit has been set
        /** @var Builder $query */
        $query = tap($this->getPastableQuery(), function (Builder $query) {
            $query->when(! $query->getQuery()->limit, function ($query) {
                $query->limit(config('pastable.chunkSize', 1000));
            });
        });

        //Check the table exists
        $tableName = $this->getPastableTable();
        $this->log("Checking for target table '{$tableName}'");
        if (! $this->createTable($query)) {
            throw new Exception('[Pastable] Unable to find or create the target table: '.$tableName);
        }

        $this->log("Using pastable query '{$query->toSql()}'");

        return $query;
    }

    protected function createTable(Builder $query): bool
    {
        $connection = $this->getPastableConnection();
        $tableName = $this->getPastableTable();

        //Does a target exist?
        if (! DB::connection($connection)->table($tableName)->exists()) {
            //Should it be created?
            if (config('pastable.autoCreate', false)) {
                //Create table
                Schema::connection($connection)->create($tableName, function (Blueprint $table) use ($query) {
                    $columns = $query->getColumns();

                    foreach ($columns as $column) {
                        $type = Schema::getColumnType(static::getTable(), $column);
                        try {
                            $table->{$type}($column);

                        } catch (Throwable $t) {
                            $table->addColumn($type, $column);
                        }
                    }
                });
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Define a string describing the target table name
     *
     * @return string The target table name
     */
    public function getPastableTable(): string
    {
        return property_exists(static::class, 'pastableTable')
            ? $this->pastableTable
            : throw new LogicException('[Pastable] Please set the `pastableTable` property or override `getPastableTable`!');
    }

    /**
     * Define the connection you want to leverage, or default (null)
     *
     * @return ?string
     */
    public function getPastableConnection(): ?string
    {
        return property_exists(static::class, 'pastableConnection')
            ? $this->pastableConnection
            : null;
    }

    /**
     * Get the pastable model query.
     *
     * @return Builder A query of to be selected and cut/copied data
     */
    public function getPastableQuery(): Builder
    {
        throw new LogicException('[Pastable] Please implement the `getPastableQuery` function on your model.');
    }
}
