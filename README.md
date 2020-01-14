# bref-sqs-laravel

Laravel adapter for bref

TODOs: 

* [ ] Partial failures should not "re-send" new messages, instead we should delete successful messages and throw an exception if at least 1 job failed inside the batchsize
* [ ] (In case the above point will work - this becames obsolete) Dead-Letter-Queue support (native by reading the AWS settings or custom?)

## Example artisan.php

```php
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\StringInput(getenv('ARTISAN_COMMAND')),
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);
```

## Example serverless.yml

```yaml
functions:
    queue:
        handler: artisan.php
        environment:
            ARTISAN_COMMAND: 'sqs:work sqs --tries=3 --sleep=1 --delay=1'
        layers:
            - ${bref:layer.php-73}
        events:
            - sqs:
                  arn: arn:aws:sqs:region:XXXXXX:myQueue
                  batchSize: 10
```
