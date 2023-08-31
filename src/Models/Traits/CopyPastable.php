<?php

namespace ElipZis\Pastable\Models\Traits;

use Exception;
use Illuminate\Support\Facades\DB;

trait CopyPastable
{
    use Pastable;

    /**
     * @return int The amount of affected rows
     *
     * @throws Exception
     */
    public function copyAndPaste()
    {
        $query = $this->preparePastable($this->getPastableQuery());
        $connection = $this->getPastableConnection();
        $tableName = $this->getPastableTable();

        //Copy and Paste
        $affected = DB::connection($connection)->table($tableName)->insertUsing($query->getQuery()->columns ?? [], $query);
        $this->log("Affected {$affected} rows");

        return $affected;
    }
}
