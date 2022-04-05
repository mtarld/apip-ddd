# Hexagonal API Platform 3

An example project using **API Platform 3** following the **hexagonal architecture**.

This example has been explained during the [SymfonyLive Paris 2022](https://live.symfony.com/2022-paris/schedule#session-625)
([slides](https://slides.com/mathiasarlaud/sflive-apip-ddd)).

## Getting started
If you wan't to try to use and tweak that example, you can follow these steps:

1. Run `git clone https://github.com/mtarld/apip-ddd` to clone the project
1. Run `docker-compose build --pull --no-cache` to build fresh images
1. Run `docker-compose up` to up your containers
1. Visit https://localhost and play with your app!

## :warning: Temporary dependencies

As [API platform](https://github.com/api-platform/core) 3 isn't released yet, that repository is using the `dev-main`
branch of `api-platform/core`.
As soon as API Platform is released (it should happen very soon), the `v3` tag must be targetterd and the `minimum-stability` must be updated.

## What's inside?
Following links aim to explain the architecture of the project and the purpose of each classes.

- [Layers](docs/layers.md) (TODO)
- Domain layer
  - [Models and repositories](docs/domain/models_and_repositories.md) (TODO)
- Application layer
  - [The command/query pattern](docs/application/command_query_pattern.md) (TODO)
- Infrastructure layer
  - [API Resource](docs/infrastructure/api_resource.md) (TODO)
  - [Custom operation metadata](docs/infrastructure/custom_operation_metadata.md) (TODO)
  - [Query providers](docs/infrastructure/query_providers.md)
  - [Command processors](docs/infrastructure/command_processors.md) (WIP)
  - [CRUD providers/processors](docs/infrastructure/crud_providers_processors.md) (TODO)
  - [Command data transformers](docs/infrastructure/command_data_transformers.md) (TODO)
  - [Openapi filters](docs/infrastructure/openapi_filters.md) (TODO)
  - [Native providers/processors removal compiler pass](docs/infrastructure/native_providers_processors_removal_compiler_pass.md)
  - [Messenger buses](docs/infrastructure/messenger_buses.md) (TODO)

## Contributing
That implementation is pragmatic and far for being uncriticable.
It's mainly an conceptual approach to extend API Platform in order to defer operations to command and query buses.

It could and should be improved, therefore feel free to submit issues and pull requests if something isn't relevant to your use cases or isn't clean enough.

## Authors
[Mathias Arlaud](https://github.com/mtarld) with the help of [Robin Chalas](https://github.com/chalasr)
