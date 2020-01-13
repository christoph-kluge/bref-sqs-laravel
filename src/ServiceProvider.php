<?php namespace Sikei\Bref\Sqs\Laravel;

use Sikei\Bref\Sqs\Laravel\Commands\SqsWorkCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SqsWorkCommand::class,
            ]);
        }
    }
}
