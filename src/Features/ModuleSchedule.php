<?php


namespace Fnp\ElModule\Features;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;

trait ModuleSchedule
{
    abstract public function defineSchedule(Schedule $scheduler);

    public function bootModuleScheduleFeature(Application $application)
    {
        if (!$application->runningInConsole())
            return;

        $application->booted(function () use ($application) {
            $schedule = $application->make(Schedule::class);
            $this->defineSchedule($schedule);
        });
    }
}