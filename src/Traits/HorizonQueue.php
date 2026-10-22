<?php

namespace Pijler\LaravelCommon\Traits;

trait HorizonQueue
{
    /**
     * Set the job to run on the low priority queue.
     */
    public function onLowPriority(): void
    {
        $this->onQueue('low-priority');
    }

    /**
     * Set the job to run on the medium priority queue.
     */
    public function onMediumPriority(): void
    {
        $this->onQueue('medium-priority');
    }

    /**
     * Set the job to run on the high priority queue.
     */
    public function onHighPriority(): void
    {
        $this->onQueue('high-priority');
    }
}
