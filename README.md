# bref-sqs-laravel

Laravel adapter for bref

TODOs: 

* [ ] Dead-Letter-Queue support (native by reading the AWS settings or custom?)
* [ ] Test and document `serverless.yml` to automatically setup lambda to receive sqs events  

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
```
