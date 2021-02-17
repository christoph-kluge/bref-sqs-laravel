# Serverless laravel queues on AWS Lambda (SQS driver)

Do you have scaling problems with your laravel queues? Install, deploy and bother less!

Thanks to the severless approach it's very easy to scale parts of your software.
This projects adds native laravel queue support.

Thanks to `brefphp/bref` and `serverless/serverless` which do the heavy lifing here.

## Supported bref versions

| Version | Branch | Status                       |
| ---     | ---    | ---                          |
| 1.2.x   | master | supported                    |
| 1.1.x   | master | untested - perhaps supported |
| 0.5.x   | master | supported                    |

## Supported laravel versions

| Version | Branch | Status    |
| ---     | ---    | ---       |
| 8.x     | master | supported |
| 7.x     | master | supported |
| 6.x     | master | supported |
| 5.8     | master | supported |
| < 5.8   | master | unknown   |

## Install

To install via Composer, use the command below. It will automatically detect the latest version and bind it with ^.

```
composer require christoph-kluge/bref-sqs-laravel
```

This package will automatically register the ServiceProvider within your laravel application.

## Usage instructions

1. Configure your application to use SQS queues (please refer to the official laravel documentation)
2. Install this package through composer 
3. Add the example `artisan.php` to the root directory of your project
4. Update your `serverless.yml` with a new handler using the `artisan.php`

### Example artisan.php

```php
#!/opt/bin/php
<?php declare(strict_types=1);

$appRoot = getenv('LAMBDA_TASK_ROOT');
require_once $appRoot . '/vendor/autoload.php';
require_once $appRoot . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = app(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\StringInput(getenv('ARTISAN_COMMAND')),
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

```

### Example serverless.yml

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
                  arn: arn:aws:sqs:region:XXXXXX:default-queue
                  batchSize: 10

    another-queue:
        handler: artisan.php
        environment:
            ARTISAN_COMMAND: 'sqs:work sqs --queue=another-queue --tries=3 --sleep=1 --delay=1'
        layers:
            - ${bref:layer.php-73}
        events:
            - sqs:
                  arn: arn:aws:sqs:region:XXXXXX:another-queue
                  batchSize: 10
```

## TODOs

* [ ] Test FIFO queues
* [ ] Partial failures should not "re-send" new messages, instead we should delete successful messages and throw an exception if at least 1 job failed inside the batchsize
* [ ] (In case the above point will work - this becames obsolete) Dead-Letter-Queue support (native by reading the AWS settings or custom?)

## References / Links / Insights

Useful links and insights about this topic: 

* https://serverless.com/framework/docs/providers/aws/guide/serverless.yml/
* https://github.com/brefphp/bref/issues/421
* https://nordcloud.com/amazon-sqs-as-a-lambda-event-source/
* https://lumigo.io/blog/sqs-and-lambda-the-missing-guide-on-failure-modes/
