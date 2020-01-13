<?php namespace Sikei\Bref\Sqs\Laravel\Queue;

use Illuminate\Queue\Jobs\SqsJob;

class Job extends SqsJob
{

    public function release($delay = 0)
    {
        $this->released = true;

        $payload = $this->payload();
        if (!array_key_exists('attempts', $payload)) {
            $payload['attempts'] = 1;
        }
        $payload['attempts']++;

        $this->sqs->deleteMessage([
            'QueueUrl' => $this->queue,
            'ReceiptHandle' => $this->job['ReceiptHandle'],
        ]);

        $this->sqs->sendMessage([
            'QueueUrl' => $this->queue,
            'MessageBody' => json_encode($payload),
            'DelaySeconds' => $this->secondsUntil($delay),
        ]);
    }

    public function attempts()
    {
        $payload = $this->payload();
        if (array_key_exists('attempts', $payload)) {
            return (int)$payload['attempts'];
        }

        return parent::attempts();
    }
}
