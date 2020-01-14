<?php namespace Sikei\Bref\Sqs\Laravel\Commands;

use Bref\Runtime\LambdaRuntime;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Queue\Worker;
use Sikei\Bref\Sqs\Laravel\Queue\Connector;
use Sikei\Bref\Sqs\Laravel\Queue\Queue;

class SqsWorkCommand extends WorkCommand
{

    protected $signature = 'sqs:work
                            {connection? : The name of the queue connection to work}
                            {--queue= : The names of the queues to work}
                            {--daemon : Run the worker in daemon mode (Deprecated)}
                            {--once : Only process the next job on the queue}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--delay=0 : The number of seconds to delay failed jobs}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=0 : Number of times to attempt a job before logging it failed}';

    /** @var LambdaRuntime */
    protected $lambdaRuntime;

    /** @var \Illuminate\Queue\Worker */
    protected $worker;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    public function __construct(Worker $worker, Cache $cache)
    {
        Command::__construct();

        $this->cache = $cache;
        $this->worker = $worker;
    }

    public function handle()
    {
        $this->lambdaRuntime = LambdaRuntime::fromEnvironmentVariable();

        // Add custom connector, which will expose the "fill" method for the SQS event
        $this->worker->getManager()->addConnector('sqs', function () {
            return new Connector();
        });

        parent::handle();
    }

    protected function runWorker($connection, $queueName)
    {
        $this->worker->setCache($this->cache);

        /** @var Queue $queue */
        $queue = $this->worker->getManager()->connection($connection);

        while (true) {
            $this->lambdaRuntime->processNextEvent(function (array $event) use ($connection, $queueName, $queue) : array {
                $queue->fill($event);

                while ($queue->size($queue) > 0) {
                    $this->worker->runNextJob($connection, $queueName, $this->gatherWorkerOptions());
                }

                return [];
            });
        }
    }

}
