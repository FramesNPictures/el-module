<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleCacheEvents;
use Fnp\ElModule\Features\ModuleConsoleCommands;
use Fnp\ElModule\Features\ModuleEventListeners;
use Fnp\ElModule\Features\ModuleMigrations;
use Fnp\ElModule\Features\ModuleOptimizationEvents;
use Fnp\ElModule\Features\ModuleSchedule;
use Fnp\ElModule\Features\ModuleSetupConsoleApplication;
use Fnp\ElModule\Features\ModuleSetupWebApplication;
use Fnp\ElModule\Tests\Fixtures\Support\RecordingListener;
use Fnp\ElModule\Tests\Fixtures\Support\SampleCommand;
use Fnp\ElModule\Tests\Fixtures\Support\SecondListener;
use Fnp\ElModule\Tests\Fixtures\Support\ThingHappened;
use Fnp\ElModule\Tests\TestCase;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ConsoleLifecycleFeaturesTest extends TestCase
{
    /* -------------------------------------------------------------------- */
    /* Event listeners                                                       */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function single_event_listeners_are_registered(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleEventListeners;

            public function defineEventListeners(): array
            {
                return [ThingHappened::class => RecordingListener::class];
            }
        };

        $this->app->register($module);

        $this->assertTrue($this->app['events']->hasListeners(ThingHappened::class));
        $this->assertCount(1, $this->app['events']->getListeners(ThingHappened::class));
    }

    #[Test]
    public function an_array_of_listeners_registers_each_one(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleEventListeners;

            public function defineEventListeners(): array
            {
                return [
                    ThingHappened::class => [RecordingListener::class, SecondListener::class],
                ];
            }
        };

        $this->app->register($module);

        $this->assertCount(2, $this->app['events']->getListeners(ThingHappened::class));
    }

    /* -------------------------------------------------------------------- */
    /* Console commands (register phase, console only)                       */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function console_commands_are_registered_when_running_in_console(): void
    {
        $module = $this->capturingConsoleCommandsModule();

        // PHPUnit runs in the console, so the command list is forwarded.
        $this->app->register($module);

        $this->assertSame([SampleCommand::class], $module->captured);
    }

    #[Test]
    public function console_commands_are_skipped_outside_the_console(): void
    {
        $module = $this->capturingConsoleCommandsModule();

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(false);

        $module->registerModuleConsoleCommandsFeature($app);

        $this->assertSame([], $module->captured);
    }

    /* -------------------------------------------------------------------- */
    /* Migrations (boot phase, console only)                                 */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function migrations_are_loaded_when_running_in_console(): void
    {
        $module = $this->capturingMigrationsModule();

        $this->app->register($module);

        $this->assertSame(['/tmp/el-module-migrations'], $module->captured);
    }

    #[Test]
    public function migrations_are_skipped_outside_the_console(): void
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(false);

        $module = new class($app) extends ElModule {
            use ModuleMigrations;

            public array $captured = [];

            public function defineMigrationFolders(): array
            {
                return ['/tmp/el-module-migrations'];
            }

            public function loadMigrationsFrom($paths): void
            {
                $this->captured = (array) $paths;
            }
        };

        $module->bootModuleMigrationsFeature();

        $this->assertSame([], $module->captured);
    }

    /* -------------------------------------------------------------------- */
    /* Schedule (boot phase, console only)                                   */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function schedule_is_defined_when_running_in_console(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleSchedule;

            public ?Schedule $schedule = null;

            public function defineSchedule(Schedule $scheduler): void
            {
                $this->schedule = $scheduler;
                $scheduler->call(fn () => null)->name('el-module-task');
            }
        };

        $this->app->register($module);

        $this->assertInstanceOf(Schedule::class, $module->schedule);
        $this->assertNotEmpty($module->schedule->events());
    }

    #[Test]
    public function schedule_is_skipped_outside_the_console(): void
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(false);
        // booted() must never be reached when not in the console.
        $app->shouldNotReceive('booted');

        $module = new class($app) extends ElModule {
            use ModuleSchedule;

            public bool $defined = false;

            public function defineSchedule(Schedule $scheduler): void
            {
                $this->defined = true;
            }
        };

        $module->bootModuleScheduleFeature($app);

        $this->assertFalse($module->defined);
    }

    /* -------------------------------------------------------------------- */
    /* Setup console / web application                                       */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function console_setup_runs_in_the_console(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleSetupConsoleApplication;

            public bool $ran = false;

            public function setupConsoleApplication(Application $application): void
            {
                $this->ran = true;
            }
        };

        $this->app->register($module);

        $this->assertTrue($module->ran);
    }

    #[Test]
    public function web_setup_does_not_run_in_the_console(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleSetupWebApplication;

            public bool $ran = false;

            public function setupWebApplication(Application $application): void
            {
                $this->ran = true;
            }
        };

        $this->app->register($module);

        $this->assertFalse($module->ran, 'Web setup must be inert under the console.');
    }

    #[Test]
    public function web_setup_runs_for_web_requests(): void
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(false);

        $module = new class($app) extends ElModule {
            use ModuleSetupWebApplication;

            public bool $ran = false;

            public function setupWebApplication(Application $application): void
            {
                $this->ran = true;
            }
        };

        $module->bootModuleSetupWebApplicationFeature($app);

        $this->assertTrue($module->ran);
    }

    /* -------------------------------------------------------------------- */
    /* Optimization hooks (CommandFinished events, Laravel 11+)               */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function optimization_hook_runs_when_the_optimize_command_finishes(): void
    {
        $module = $this->optimizationModule();
        $this->app->register($module);

        $this->dispatchCommandFinished('optimize');

        $this->assertTrue($module->optimized, 'onOptimization() should run for `optimize`.');
        $this->assertFalse($module->cleared);
    }

    #[Test]
    public function clear_hook_runs_when_the_optimize_clear_command_finishes(): void
    {
        $module = $this->optimizationModule();
        $this->app->register($module);

        $this->dispatchCommandFinished('optimize:clear');

        $this->assertTrue($module->cleared, 'onOptimizationClear() should run for `optimize:clear`.');
        $this->assertFalse($module->optimized);
    }

    #[Test]
    public function optimization_hooks_ignore_unrelated_commands(): void
    {
        $module = $this->optimizationModule();
        $this->app->register($module);

        // Sub-commands fired internally by `optimize` (config:cache, etc.) and
        // any other command must not trigger the module hooks.
        $this->dispatchCommandFinished('config:cache');
        $this->dispatchCommandFinished('migrate');

        $this->assertFalse($module->optimized);
        $this->assertFalse($module->cleared);
    }

    /* -------------------------------------------------------------------- */
    /* Cache events (CommandFinished events, Laravel 11+)                     */
    /* -------------------------------------------------------------------- */

    #[Test]
    public function cache_cleared_hook_runs_when_the_cache_clear_command_finishes(): void
    {
        $module = $this->cacheEventsModule();
        $this->app->register($module);

        $this->dispatchCommandFinished('cache:clear');

        $this->assertTrue($module->cleared, 'onCacheCleared() should run for `cache:clear`.');
    }

    #[Test]
    public function cache_cleared_hook_ignores_unrelated_commands(): void
    {
        $module = $this->cacheEventsModule();
        $this->app->register($module);

        $this->dispatchCommandFinished('cache:forget');
        $this->dispatchCommandFinished('optimize:clear');
        $this->dispatchCommandFinished('migrate');

        $this->assertFalse($module->cleared);
    }

    /* -------------------------------------------------------------------- */
    /* Helpers                                                               */
    /* -------------------------------------------------------------------- */

    private function cacheEventsModule(): ElModule
    {
        return new class($this->app) extends ElModule {
            use ModuleCacheEvents;

            public bool $cleared = false;

            public function onCacheCleared(): void
            {
                $this->cleared = true;
            }
        };
    }

    private function optimizationModule(): ElModule
    {
        return new class($this->app) extends ElModule {
            use ModuleOptimizationEvents;

            public bool $optimized = false;
            public bool $cleared   = false;

            public function onOptimization(): void
            {
                $this->optimized = true;
            }

            public function onOptimizationClear(): void
            {
                $this->cleared = true;
            }
        };
    }

    private function dispatchCommandFinished(string $command): void
    {
        $this->app['events']->dispatch(
            new CommandFinished($command, new ArrayInput([]), new NullOutput(), 0)
        );
    }

    private function capturingConsoleCommandsModule(): ElModule
    {
        return new class($this->app) extends ElModule {
            use ModuleConsoleCommands;

            public array $captured = [];

            public function defineConsoleCommands(): array
            {
                return [SampleCommand::class];
            }

            public function commands($commands): void
            {
                $this->captured = (array) $commands;
            }
        };
    }

    private function capturingMigrationsModule(): ElModule
    {
        return new class($this->app) extends ElModule {
            use ModuleMigrations;

            public array $captured = [];

            public function defineMigrationFolders(): array
            {
                return ['/tmp/el-module-migrations'];
            }

            public function loadMigrationsFrom($paths): void
            {
                $this->captured = (array) $paths;
            }
        };
    }
}
