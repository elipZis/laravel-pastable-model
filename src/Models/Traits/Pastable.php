<?php

namespace ElipZis\Pastable\Models\Traits;

use ElipZis\Pastable\Helper\PastableLogger;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LogicException;
use Throwable;

trait Pastable
{
    use PastableLogger;

    /**
     * @throws Exception
     */
    protected function preparePastable(Builder $query): Builder
    {
        //Check the table exists
        $tableName = $this->getPastableTable();
        $this->log("Checking for target table '{$tableName}'");
        if (!$this->createTable($query)) {
            throw new Exception('[Pastable] Unable to find or create the target table: ' . $tableName);
        }

        $this->log("Using pastable query '{$query->toSql()}'");

        return $query;
    }

    /**
     * Check if a table exists or is to be created
     */
    protected function createTable(Builder $query): bool
    {
        $connection = $this->getPastableConnection();
        $tableName = $this->getPastableTable();

        //Does a target exist?
        if (!Schema::connection($connection)->hasTable($tableName)) {
            $this->log("Table `{$tableName}` does not exist");

            //Should it be created?
            if (config('pastable.autoCreate', false)) {
                $this->log("Trying to create `{$tableName}` table");

                //Create table
                Schema::connection($connection)->create($tableName, function (Blueprint $table) use ($query) {
                    $columns = $query->getQuery()->columns ?? Schema::getColumnListing(static::getTable());

                    foreach ($columns as $column) {
                        $type = Schema::getColumnType(static::getTable(), $column);

                        //Resolve some types
                        if (str_contains($type, 'int')) {
                            $type = 'bigInteger'; //Always fall back to big ints to be sure
                        } elseif ($type === 'datetime') {
                            $type = 'dateTime';
                        }

                        try {
                            $this->log("Trying to call type function for column `{$column}` of type {$type}");
                            $table->{$type}($column);

                        } catch (Throwable $t) {
                            $this->log("Failed! Trying to add column `{$column}` of type {$type}");
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
