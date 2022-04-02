# Command processors
[Back](../../README.md)

To handle our [commands](../application/command_query_pattern.md),
we added some API Platform processors dedicated to it.

Processors in API Platform are **responsible to "do something" based on request data**.
You can find them in `src/Infrastructure/[BoundedContext]/ApiPlatform/State/Processor/`.

The aim of these processors is to **do something using the command bus** and therefore
using the application and domain layer.
You can see them as the link between the
[API Resource](../infrastructure/api_resource.md)'s operation (in the infrastructure layer)
and the [command handlers](../application/command_query_pattern.md).

API Platform is designed in a way that when a processor is triggered, the command has already been instanciated.
Therefore, unlike [providers](./query_providers.md), processors don't have to instanciate any command itself.
It's rather the role of [command data transformers](./command_data_transformers.md).

In this example, the way we chosen to go is to create **one processor per command**.
In that way, we are the most flexible possible, and we are able to handle
each use case precisely.
But if you have plenty of commands which are quite similar, there is no counter-argument
to create a generic processor that'll act as a fallback processor
(be careful to make it less prioritized that specific processor though).

## Scoping command processors
As there is one processor per command, each processor must be scoped to handle its related command.
This could be done like the following:
```php
use App\Application\[BoundedContext]\Command\MyCommand;

public function supports($data, array $identifiers = [], ?string $operationName = null, array $context = []): bool
{
    return $data instanceof MyCommand;
}
```

## Processing data
TODO

## Command processors priority

In order to process data through API Resource operation, API Platform relies on a `ChainProcessor`.
This special service is iterating over every known processor, which can be recognized thanks to the `api_platform.state_processor` tag.

To sort processors, the `ChainProcessor` is relying on the property `priority` associated to that tag.
You can check actual priorities running the follwing command:
```
$ bin/console debug:container --tag=api_platform.state_processor
--------------- ----------
 Service ID      priority
--------------- ----------
 MyFooProcessor   1
 MyBarProcessor   0
--------------- ----------
```

And because these command processors aren't the only available processors in the application, **be careful to have a proper
priority so that they'll be executed first** (before the [CRUD processors](./crud_providers_processors.md) for example).
This could be configured in your service definition:
```php
// config/services/[bounded_context].php

$services->set(MyFooProcessor::class)
    ->autoconfigure(false)
    ->tag('api_platform.state_processor', ['priority' => 1]);

$services->set(MyCrudProcessor::class)
    ->autoconfigure(false)
    ->tag('api_platform.state_processor', ['priority' => 0]);
```
