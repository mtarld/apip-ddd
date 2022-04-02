# Native providers/processors removal compiler pass
[Back](../../README.md)

In order to provide/process data through API Resource operation,
API Platform relies on a `ChainProvider` and a `ChainProcessor`.

These special services are iterating over every known providers/processors,
which can be recognized thanks to the `api_platform.state_provider` and `api_platform.state_processor` tags.

To handle our [queries and commands](../application/command_query_pattern.md), we added some [custom query providers](./query_providers.md),
[custom command processors](./command_processors.md) and some [CRUD providers/processors](./crud_providers_processors.md) to handle our use cases.

But what happens if we forgot to add the provider/processor to an operation?

Necessarily, the `ChainProvider` and the `ChainProcessor` will fallback on native providers/processors
and interact with data in a way that we don't expect neither want to.

Here are the initial providers and processors of the application:

```
$ bin/console debug:container --tag=api_platform.state_provider
----------------------------------------------------------------------------- ----------
 Service ID                                                                    priority
----------------------------------------------------------------------------- ----------
 App\Infrastructure\Library\ApiPlatform\State\Provider\CheapestBooksProvider   1
 App\Infrastructure\Library\ApiPlatform\State\Provider\BookCrudProvider        0
 api_platform.doctrine.orm.state.collection_provider                           -100
 api_platform.doctrine.orm.state.item_provider                                 -100
 api_platform.legacy_data_provider_state                                       -1000
----------------------------------------------------------------------------- ----------

$ bin/console debug:container --tag=api_platform.state_processor
-------------------------------------------------------------------------------- ----------
 Service ID                                                                       priority
-------------------------------------------------------------------------------- ----------
 App\Infrastructure\Library\ApiPlatform\State\Processor\AnonymizeBooksProcessor   1
 App\Infrastructure\Library\ApiPlatform\State\Processor\DiscountBookProcessor     1
 App\Infrastructure\Library\ApiPlatform\State\Processor\BookCrudProcessor         0
 api_platform.doctrine.orm.state.processor                                        -100
 api_platform.messenger.processor                                                 -900
-------------------------------------------------------------------------------- ----------
```

To prevent that behavior, we can untag native providers/processors so that they won't be recognized and used by API Platform.
That's the very purpose of the `ClearNativeProviderAndProcessorsCompilerPass` compiler pass.

After that compiler pass added, no native providers/processors will be considered as such.
API Platform won't be able to fallback to them and will instead raise and exception that we can detect and fix.

```
$ bin/console debug:container --tag=api_platform.state_provider
----------------------------------------------------------------------------- ----------
 Service ID                                                                    priority
----------------------------------------------------------------------------- ----------
 App\Infrastructure\Library\ApiPlatform\State\Provider\CheapestBooksProvider   1
 App\Infrastructure\Library\ApiPlatform\State\Provider\BookCrudProvider        0
----------------------------------------------------------------------------- ----------

$ bin/console debug:container --tag=api_platform.state_processor
-------------------------------------------------------------------------------- ----------
 Service ID                                                                       priority
-------------------------------------------------------------------------------- ----------
 App\Infrastructure\Library\ApiPlatform\State\Processor\AnonymizeBooksProcessor   1
 App\Infrastructure\Library\ApiPlatform\State\Processor\DiscountBookProcessor     1
 App\Infrastructure\Library\ApiPlatform\State\Processor\BookCrudProcessor         0
-------------------------------------------------------------------------------- ----------
```
