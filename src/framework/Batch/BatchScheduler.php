<?php

namespace framework\Batch;

class BatchScheduler
{
    protected $jobs = [];

    public function addJob(BatchJob $job)
    {
        $this->jobs[] = $job;
    }

    public function runJobs()
    {
        foreach ($this->jobs as $job) {
            if ($job->shouldRun()) {
                $job->handle();
            }
        }
    }
}
