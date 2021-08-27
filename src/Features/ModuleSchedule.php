<?php

namespace Fnp\ElModule\Features;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;

trait ModuleSchedule
{
    /**
     * Use Schedule object to schedule the jobs and tasks
     * usual Laravel way.
     *
     * @param  Schedule  $scheduler
     *
     * @return void
     */
    abstract public function defineSchedule(Schedule $scheduler): void;

    public function bootModuleScheduleFeature(Application $application)
    {
        if (!$application->runningInConsole()) {
            return;
        }

        $application->booted(function () use ($application) {
            $schedule = $application->make(Schedule::class);
            $this->defineSchedule($schedule);
        });
    }
}