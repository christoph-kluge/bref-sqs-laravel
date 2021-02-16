<?php

namespace Sikei\Bref\Sqs\Laravel;

use Sikei\Bref\Sqs\Laravel\Commands\SqsWorkCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton('command.sqs.work', function ($app) {
            return new SqsWorkCommand($app['queue.worker'], $app['cache.store']);
        });

        $this->commands(['command.sqs.work']);
    }

    public function provides()
    {
        return [
            'command.sqs.work',
        ];
    }
}
