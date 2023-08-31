<?php

namespace ElipZis\Pastable\Jobs\Middleware;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Keep a job from overlapping by keeping a lock in cache
 *
 * @see https://flareapp.io/blog/7-how-to-safely-delete-records-in-massive-tables-on-aws-using-laravel
 */
class AtomicJob
{
    /**
     * @param string $class
     * @param int $lockTime
     */
    public function __construct(protected string $class, protected int $lockTime = 10 * 60)
    {
    }

    /**
     * Lock until completed
     *
     * @param Job $job
     * @param callable $next
     */
    public function handle($job, $next)
    {
        try {
            /** @var Lock $lock */
            $lock = Cache::getStore()->lock(get_class($job) . '_' . $this->class . '_lock', $this->lockTime);

            if (!$lock->get()) {
                $job->delete();
                return;
            }

            $next($job);
            $lock->release();
        } catch (Throwable $t) {
            $next($job);
        }
    }
}
