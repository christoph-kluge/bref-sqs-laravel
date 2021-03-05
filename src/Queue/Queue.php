<?php namespace Sikei\Bref\Sqs\Laravel\Queue;

use Illuminate\Container\Container;
use Illuminate\Queue\Queue as BaseQueue;
use Illuminate\Queue\SqsQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class Queue extends BaseQueue implements QueueContract
{

    private $items = [];
    private $sqs;

    public function __construct(SqsQueue $sqq)
    {
        $this->sqs = $sqq;
    }

    public function setContainer(Container $container)
    {
        $this->sqs->setContainer($container);

        parent::setContainer($container);
    }

    public function setConnectionName($name)
    {
        $this->sqs->setConnectionName($name);

        return parent::setConnectionName($name);
    }

    public function fill(array $event)
    {
        foreach ($event['Records'] as $record) {
            $this->items[$record['messageId']] = $record;
        }

        return $this;
    }

    public function pop($queue = null)
    {
        if (count($this->items) > 0) {
            $sqsPayload = array_shift($this->items);
            foreach ($sqsPayload as $key => $value) {
                $sqsPayload[ucfirst($key)] = $value;
            }

            return new Job($this->container, $this->sqs->getSqs(), $sqsPayload, $this->connectionName, $this->sqs->getQueue($queue));
        }
    }

    public function size($queue = null)
    {
        return count($this->items);
    }

    public function push($job, $data = '', $queue = null)
    {
        return $this->sqs->push($job, $data, $queue);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->sqs->pushRaw($payload, $queue, $options);
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->sqs->later($delay, $job, $data, $queue);
    }

}
