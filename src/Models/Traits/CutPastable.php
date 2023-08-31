<?php

namespace ElipZis\Pastable\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Throwable;

trait CutPastable
{
    use Pastable;

    /**
     * @return int
     *
     * @throws Throwable
     */
    public function cutAndPaste()
    {
        //Set a limit, if no other limit has been set
        /** @var Builder $query */
        $query = tap($this->getPastableQuery(), function (Builder $query) {
            $query->when(!$query->getQuery()->limit, function ($query) {
                $query->limit(config('pastable.chunkSize', 1000));
            });
        });

        $query = $this->preparePastable($query);
        $connection = $this->getPastableConnection();
        $tableName = $this->getPastableTable();

        //Cut and Paste
        DB::beginTransaction();
        try {
            $affected = DB::connection($connection)->table($tableName)->insertUsing($query->getQuery()->columns ?? [], $query);

            //Then delete (Cutting)
            in_array(SoftDeletes::class, class_uses_recursive(static::class))
                ? $query->forceDelete()
                : $query->delete();

            DB::commit();
        } catch (Throwable $t) {
            DB::rollBack();
            $this->log('Error while cut-paste: ' . $t->getMessage());
            throw $t;
        }

        return $affected;
    }
}
