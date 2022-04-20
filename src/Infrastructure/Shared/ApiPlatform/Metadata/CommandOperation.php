<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\ApiPlatform\Metadata;

use ApiPlatform\Metadata\HttpOperation;
use App\Application\Shared\Command\CommandInterface;

final class CommandOperation extends HttpOperation
{
    /**
     * @var class-string<CommandInterface>
     */
    private string $command;

    /**
     * @param class-string<CommandInterface> $command
     */
    public function __construct(
        ?string $uriTemplate = null,
        ?string $command = null,
        ?array $types = null,
        $formats = null,
        $inputFormats = null,
        $outputFormats = null,
        $uriVariables = null,
        ?string $routePrefix = null,
        ?string $routeName = null,
        ?array $defaults = null,
        ?array $requirements = null,
        ?array $options = null,
        ?bool $stateless = null,
        ?string $sunset = null,
        ?string $acceptPatch = null,
        $status = null,
        ?string $host = null,
        ?array $schemes = null,
        ?string $condition = null,
        ?string $controller = null,
        ?array $cacheHeaders = null,

        ?array $hydraContext = null,
        ?array $openapiContext = null,
        ?array $exceptionToStatus = null,

        ?bool $queryParameterValidationEnabled = null,

        ?string $shortName = null,
        ?string $class = null,
        ?bool $paginationEnabled = null,
        ?string $paginationType = null,
        ?int $paginationItemsPerPage = null,
        ?int $paginationMaximumItemsPerPage = null,
        ?bool $paginationPartial = null,
        ?bool $paginationClientEnabled = null,
        ?bool $paginationClientItemsPerPage = null,
        ?bool $paginationClientPartial = null,
        ?bool $paginationFetchJoinCollection = null,
        ?bool $paginationUseOutputWalkers = null,
        ?array $paginationViaCursor = null,
        ?array $order = null,
        ?string $description = null,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $security = null,
        ?string $securityMessage = null,
        ?string $securityPostDenormalize = null,
        ?string $securityPostDenormalizeMessage = null,
        ?string $securityPostValidation = null,
        ?string $securityPostValidationMessage = null,
        ?string $deprecationReason = null,
        ?array $filters = null,
        ?array $validationContext = null,
        $input = null,
        $output = null,
        $mercure = null,
        $messenger = null,
        ?bool $elasticsearch = null,
        ?int $urlGenerationStrategy = null,
        ?bool $read = null,
        ?bool $deserialize = null,
        ?bool $validate = null,
        ?bool $write = null,
        ?bool $serialize = null,
        ?bool $fetchPartial = null,
        ?bool $forceEager = null,
        ?int $priority = null,
        ?string $name = null,
        ?string $provider = null,
        ?string $processor = null,
        array $extraProperties = []
    ) {
        $this->command = $command;

        $args = func_get_args();

        // remove the extra command parameter
        unset($args[1]);

        parent::__construct(self::METHOD_POST, ...$args);
    }

    /**
     * @return class-string<CommandInterface>
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param class-string<CommandInterface> $command
     */
    public function withCommand(string $command): static
    {
        $self = clone $this;
        $self->command = $command;

        return $self;
    }
}
