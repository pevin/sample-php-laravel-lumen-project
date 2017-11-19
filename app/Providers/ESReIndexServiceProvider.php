<?php

namespace App\Providers;

use App\Console\Commands\ESReindexCommand;
use Illuminate\Support\ServiceProvider;

class ESReIndexServiceProvider extends ServiceProvider
{
    /**
     * Register any Request Services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.es.re-index', function ($app) {
            return new ESReindexCommand($app['config']->get('es-reindex'));
        });

        $this->commands(
            'command.es.re-index'
        );
    }
}
