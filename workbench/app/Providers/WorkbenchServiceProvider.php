<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Workbench\App\Console\OrderCreate;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //

        $this->commands([OrderCreate::class]);
    }
}
