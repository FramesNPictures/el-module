<?php


namespace Fnp\Module\Features;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;

trait ModuleSchedule
{
    abstract public function schedule(Schedule $schedule);

    public function bootModuleScheduleFeature()
    {
        if (!App::runningInConsole())
            return;

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $this->schedule($schedule);
        });
    }
}