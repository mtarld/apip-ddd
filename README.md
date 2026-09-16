# Domain Driven Design and API Platform 4

An example project of **Domain Driven Design** using **API Platform 4** and following the **hexagonal architecture**.

The first version of this project has been explained during the [API Platform conference 2022](https://api-platform.com/con/2022/conferences/domain-driven-design-with-api-platform-3/)
([slides](https://slides.com/mathiasarlaud/apip-con-ddd-api-p-3), [video](https://www.youtube.com/watch?v=SSQal3Msi9g))
and during [SymfonyLive Paris 2022](https://live.symfony.com/2022-paris/schedule/du-ddd-avec-api-platform).
It has since been rewritten for API Platform 4, Symfony 8.1 and PHP 8.5.

## Getting started
It requires [Docker](https://docs.docker.com/get-docker/) and [Castor](https://castor.jolicode.com/getting-started/installation/).

If you want to try to use and tweak that example, you can follow these steps:

1. Run `git clone https://github.com/mtarld/apip-ddd` to clone the project
1. Run `castor install` to install the project
1. Run `castor start` to up your containers
1. Visit https://localhost/api and play with your app!

Run `castor` on its own to list every task, `castor test` for PHPUnit and `castor ci` for the whole gate.

## What's inside
`BookStore` is the bounded context that carries the architecture. `Domain` holds the model: aggregates, value objects, repository interfaces and events. `Application` holds the use cases, one command and one handler each. `Infrastructure` holds everything that talks to the outside world: Doctrine, API Platform, Symfony, and an in-memory double of every repository.

Two boundaries are enforced in CI. `deptrac_bc.yaml` keeps `BookStore` and `Subscription` from knowing about each other, `deptrac_hexa.yaml` keeps the layers in order.

Writes go through the model. A processor turns the payload into a command, the handler loads an aggregate and changes it, and nothing comes back: a command that answers cannot be routed to a transport, so the identity is handed in rather than asked for. Reads go around the model instead, through a DQL projection, so loading a book to rename it no longer loads its reviews.

Five relations are modelled, each for a different rule. A review lives inside its book and is only reachable through it. An author is referenced by identity, never by association. A classification is a link that carries its own date, so it is an entity rather than a join table. An order copies the name and price it bought, so repricing a book does not rewrite history. And `Subscription` hears about a new book through a published event, not through an import.

`Subscription` is deliberately not that. It is a Doctrine entity exposed directly as an `#[ApiResource]`, there to show that this architecture is a cost you pay where it buys you something, not something to apply to every table.

The same commands serve two transports: `/api` for browsers, `/mcp` for agents. A tool reuses the processor that already serves HTTP, so an agent goes through the same command bus and the same aggregates.

Every repository has two implementations, Doctrine and in-memory, and a single test suite that both have to pass.

## Contributing
This implementation is pragmatic and far from uncriticisable. It is a conceptual approach to using API Platform on top of a domain model, not a template to copy verbatim.

Issues and pull requests are welcome, especially if something does not fit your use case. Please make sure `castor ci` passes before opening one.

## Authors
[Mathias Arlaud](https://github.com/mtarld) with the help of [Robin Chalas](https://github.com/chalasr)
