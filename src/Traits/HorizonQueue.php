<?php

namespace Common\Traits;

trait HorizonQueue
{
    /**
     * Set the job to run on the low priority queue.
     */
    public function onLowPriority(): void
    {
        $this->onQueue('low-priority');

        $this->onConnection(config('queue.long_connection'));
    }

    /**
     * Set the job to run on the medium priority queue.
     */
    public function onMediumPriority(): void
    {
        $this->onQueue('medium-priority');

        $this->onConnection(config('queue.medium_connection'));
    }

    /**
     * Set the job to run on the high priority queue.
     */
    public function onHighPriority(): void
    {
        $this->onQueue('high-priority');

        $this->onConnection(config('queue.short_connection'));
    }
}
