<?php

namespace ElipZis\Pastable\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Events\ModelsPruned;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait CopyPastable
{
    use Pastable;

    public function paste()
    {
        $tableName = $this->getPasteableTable();

        //Set a limit, if no other limit has been set
        /** @var Builder $query */
        $query = tap($this->pasteableQuery(), function (Builder $query) {
            $query->when(!$query->getQuery()->limit, function ($query) {
                $query->limit(1000);
            });
        });


        Log::debug('[CopyPasteable] Using pasteable query: ' . $query->toSql());

        //Paste
        $affected = DB::table($tableName)->insertUsing($query->getQuery()->columns ?? [], $query);

        Log::debug('[CopyPasteable] Affected rows: ' . $affected);

//        //Then delete (only for Cutting)
//        in_array(SoftDeletes::class, class_uses_recursive(static::class))
//            ? $query->forceDelete()
//            : $query->delete();

        return $affected;
    }


    public function getPasteableTable(): string
    {
        return property_exists(static::class, 'pasteableTable')
            ? $this->pasteableTable
            : throw new LogicException('[CopyPasteable] Please set the `pasteableTable` or override `getPasteableTable` property!');
    }


    /**
     * Prune all prunable models in the database.
     *
     * @param int $chunkSize
     * @return int
     */
    public function pruneAll(int $chunkSize = 1000)
    {
        $query = tap($this->prunable(), function ($query) use ($chunkSize) {
            $query->when(!$query->getQuery()->limit, function ($query) use ($chunkSize) {
                $query->limit($chunkSize);
            });
        });

        $total = 0;

        do {
            $total += $count = in_array(SoftDeletes::class, class_uses_recursive(get_class($this)))
                ? $query->forceDelete()
                : $query->delete();

            if ($count > 0) {
                event(new ModelsPruned(static::class, $total));
            }
        } while ($count > 0);

        return $total;
    }

    /**
     * Get the pasteable model query.
     *
     * @return Builder
     */
    public function pasteableQuery(): Builder
    {
        throw new LogicException('[CopyPasteable] Please implement the `pasteableQuery` method on your model.');
    }
}
