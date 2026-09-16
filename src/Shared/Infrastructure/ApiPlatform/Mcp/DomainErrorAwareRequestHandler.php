<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\Mcp;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Shared\Domain\Exception\MissingModelException;
use Mcp\Schema\JsonRpc\Error;
use Mcp\Schema\JsonRpc\Request;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Server\Handler\Request\RequestHandlerInterface;
use Mcp\Server\Session\SessionInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

/**
 * Turns a domain failure into something an agent can act on.
 *
 * @implements RequestHandlerInterface<mixed>
 */
#[AsDecorator('api_platform.mcp.handler')]
final readonly class DomainErrorAwareRequestHandler implements RequestHandlerInterface
{
    /**
     * @param RequestHandlerInterface<mixed> $decorated
     */
    public function __construct(
        #[AutowireDecorated]
        private RequestHandlerInterface $decorated,
    ) {
    }

    #[\Override]
    public function supports(Request $request): bool
    {
        return $this->decorated->supports($request);
    }

    #[\Override]
    public function handle(Request $request, SessionInterface $session): Response|Error
    {
        try {
            return $this->decorated->handle($request, $session);
        } catch (ValidationException $e) {
            return Error::forInvalidParams($e->getMessage(), $request->getId(), [
                'violations' => $this->violations($e),
            ]);
        } catch (MissingModelException $e) {
            return Error::forResourceNotFound($e->getMessage(), $request->getId());
        }
    }

    /**
     * @return list<array{property: string, message: string}>
     */
    private function violations(ValidationException $e): array
    {
        $violations = [];
        foreach ($e->getConstraintViolationList() as $violation) {
            $violations[] = [
                'property' => $violation->getPropertyPath(),
                'message' => (string) $violation->getMessage(),
            ];
        }

        return $violations;
    }
}
