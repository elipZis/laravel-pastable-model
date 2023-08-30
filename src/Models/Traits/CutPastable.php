<?php

namespace ElipZis\Pastable\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Throwable;

trait CutPastable
{
    use Pastable;

    /**
     * @return int
     * @throws Throwable
     */
    public function cutAndPaste()
    {
        $query = $this->preparePastable();

        $connection = $this->getPastableConnection();
        $tableName = $this->getPastableTable();

        //Cut and Paste
        $affected = 0;
        DB::beginTransaction();
        try {
            $affected = DB::connection($connection)->table($tableName)->insertUsing($query->getQuery()->columns ?? [], $query);

            $this->log("Affected {$affected} rows");

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
