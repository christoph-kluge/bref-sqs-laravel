<?php namespace Sikei\Bref\Sqs\Laravel\Queue;

use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Queue\SqsQueue;

class Connector extends SqsConnector
{

    public function connect(array $config)
    {
        /** @var SqsQueue $queue */
        $queue = parent::connect($config);

        return new Queue($queue);
    }
}
