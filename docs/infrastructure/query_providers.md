# Query providers
[Back](../../README.md)

To handle our [queries](../application/command_query_pattern.md),
we added some API Platform providers dedicated to it.

Providers in API Platform are **responsible to retreive data based on request data**.
You can find them in `src/Infrastructure/[BoundedContext]/ApiPlatform/State/Provider/`.

The aim of these providers is to **retreive data using the query bus** and therefore
using the application and domain layer.
You can see them as the link between the
[API Resource](../infrastructure/api_resource.md)'s operation (in the infrastructure layer)
and the [query handlers](../application/command_query_pattern.md).

In this example, the way we chosen to go is to create **one provider per query**.
In that way, we are the most flexible possible, and we are able to handle
each use case precisely.
But if you have plenty of queries which are quite similar, there is no counter-argument
to create a generic provider that'll act as a fallback provider
(be careful to make it less prioritized that specific providers though).

## Scoping query providers
As there is one provider per query, each provider must be scoped to handle its related query.
This could be done like the following:
```php
use App\Application\[BoundedContext]\Query\MyQuery;
use App\Shared\Infrastructure\ApiPlatform\Metadata\QueryOperation;

public function supports(string $resourceClass, array $identifiers = [], ?string $operationName = null, array $context = []): bool
{
    return $context['operation'] instanceof QueryOperation 
        && MyQuery::class === $context['operation']->getQuery();
}
```

## Providing data
When a provider is selected, it must return some data.
To do that, our provider will use the query bus to **dispatch the query it is bound to**
(basically the query of the operation, specified in the supports method). 

Because the query handler is located in the application layer, it'll return models.
But the provider is communicating with API Platform (that's why it is located in the infrastructure layer).

In order to have API Platform working properly (a resource is needed to use API Platform's `ItemNormalizer`),
**the provider must return resources**.

Therefore, the provider is also responsible to convert models to resources.

Here, we decided to use a resource static factory method, but you're of course free
convert models to resources the way you want (using services for example).

```php
use App\Application\[BoundedContext]\Query\MyQuery;
use App\Domain\[BoundedContext]\Model\MyModel;
use App\Infrastructure\[BoundedContext]\ApiPlatform\Resource\MyResource;

/**
 * @return MyResource
 */
public function provide(string $resourceClass, array $identifiers = [], ?string $operationName = null, array $context = []): object|array|null
{
    /** @var MyModel $model */
    $model = $this->queryBus->ask(new MyQuery());

    return MyResource::fromModel($model);
}
```

## Query providers priority

In order to provide data through API Resource operation, API Platform relies on a `ChainProvider`.
This special service is iterating over every known providers, which can be recognized thanks to the `api_platform.state_provider` tag.

To sort providers, the `ChainProvider` is relying on the property `priority` associated to that tag.
You can check actual priorities running the follwing command:
```
$ bin/console debug:container --tag=api_platform.state_provider
--------------- ----------
 Service ID      priority
--------------- ----------
 MyFooProvider   1
 MyBarProvider   0
--------------- ----------
```

And because these query providers aren't the only available providers in the application, **be careful to have a proper
priority so that they'll be executed first** (before the [CRUD providers](./crud_providers_processors.md) for example).
This could be configured in your service definition:
```php
// config/services/[bounded_context].php

$services->set(MyFooProvider::class)
    ->autoconfigure(false)
    ->tag('api_platform.state_provider', ['priority' => 1]);

$services->set(MyCrudProvider::class)
    ->autoconfigure(false)
    ->tag('api_platform.state_provider', ['priority' => 0]);
```
