<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Component\Uid\Uuid;

final class McpTest extends ApiTestCase
{
    private static ?string $sessionId = null;

    protected function setUp(): void
    {
        self::$sessionId = null;
    }

    public function testListTools(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        $this->initialize($client);
        $result = $this->rpc($client, 'tools/list');

        self::assertIsArray($result['tools']);
        $names = \array_column($result['tools'], 'name');
        \sort($names);

        self::assertSame(['anonymize_books', 'create_book', 'discount_book'], $names);

        foreach ($result['tools'] as $tool) {
            self::assertIsArray($tool);
            self::assertIsArray($tool['inputSchema']);
            self::assertSame('object', $tool['inputSchema']['type']);
        }
    }

    public function testToolSchemasCarryTheDomainBounds(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        $this->initialize($client);
        $result = $this->rpc($client, 'tools/list');

        self::assertIsArray($result['tools']);
        $tools = \array_column($result['tools'], null, 'name');

        self::assertIsArray($tools['create_book']);
        self::assertIsArray($tools['create_book']['inputSchema']);
        $properties = $tools['create_book']['inputSchema']['properties'];
        self::assertIsArray($properties);

        self::assertSame(
            ['minLength' => 1, 'maxLength' => 255, 'type' => 'string'],
            $properties['name'],
        );
        self::assertSame(
            ['minLength' => 13, 'maxLength' => 13, 'pattern' => '^\\d{13}$', 'type' => 'string'],
            \array_diff_key((array) $properties['isbn'], ['description' => null]),
        );
        self::assertSame(['minimum' => 0, 'type' => 'integer'], $properties['price']);

        self::assertIsArray($tools['discount_book']);
        self::assertIsArray($tools['discount_book']['inputSchema']);
        $discount = $tools['discount_book']['inputSchema']['properties'];
        self::assertIsArray($discount);
        self::assertSame(['format' => 'uuid', 'type' => 'string'], $discount['id']);

        self::assertSame(
            ['type' => 'string', 'format' => 'iri-reference'],
            \array_diff_key((array) $properties['author'], ['description' => null]),
        );
    }

    public function testCreateBookGoesThroughTheCommandBus(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);
        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $this->initialize($client);
        $result = $this->rpc($client, 'tools/call', [
            'name' => 'create_book',
            'arguments' => [
                'isbn' => '9782123456803',
                'name' => 'The Dispossessed',
                'description' => 'An ambiguous utopia',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1400,
            ],
        ]);

        self::assertFalse($result['isError']);
        self::assertIsArray($result['structuredContent']);
        self::assertSame('The Dispossessed', $result['structuredContent']['name']);

        self::assertCount(1, $books->all());
    }

    public function testDiscountBookTakesItsIdentifierFromTheArguments(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create(price: 1000);
        $bookRepository->add($book);

        $this->initialize($client);
        $result = $this->rpc($client, 'tools/call', [
            'name' => 'discount_book',
            'arguments' => ['id' => (string) $book->id, 'discountPercentage' => 25],
        ]);

        self::assertFalse($result['isError']);
        self::assertEquals(new Price(750), $book->price);
    }

    public function testValidationIsReportedWithItsViolations(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);
        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $this->initialize($client);
        $error = $this->rpcError($client, 'tools/call', [
            'name' => 'create_book',
            'arguments' => [
                'isbn' => '9782123456803',
                'name' => '',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => -5,
            ],
        ]);

        self::assertSame(-32602, $error['code']);
        self::assertIsArray($error['data']);
        self::assertIsArray($error['data']['violations']);
        $properties = \array_column($error['data']['violations'], 'property');
        self::assertContains('name', $properties);
        self::assertContains('price', $properties);

        self::assertCount(0, $books->all());
    }

    public function testAnInvariantAnAgentCannotGuessIsReportedAsAViolation(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);
        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $this->initialize($client);
        $error = $this->rpcError($client, 'tools/call', [
            'name' => 'create_book',
            'arguments' => [
                'isbn' => '9782123456804',
                'name' => 'The Dispossessed',
                'description' => 'An ambiguous utopia',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1400,
            ],
        ]);

        self::assertSame(-32602, $error['code']);
        self::assertIsArray($error['data']);
        self::assertIsArray($error['data']['violations']);
        $properties = \array_column($error['data']['violations'], 'property');
        self::assertContains('isbn', $properties);

        self::assertCount(0, $books->all());
    }

    public function testAMalformedIdentifierIsReportedAsAViolation(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        $this->initialize($client);
        $error = $this->rpcError($client, 'tools/call', [
            'name' => 'discount_book',
            'arguments' => ['id' => 'not-a-uuid', 'discountPercentage' => 10],
        ]);

        self::assertSame(-32602, $error['code']);
        self::assertIsArray($error['data']);
        self::assertIsArray($error['data']['violations']);
        self::assertContains('id', \array_column($error['data']['violations'], 'property'));
    }

    public function testAMissingModelIsReportedAsSuch(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        $this->initialize($client);
        $error = $this->rpcError($client, 'tools/call', [
            'name' => 'discount_book',
            'arguments' => ['id' => (string) Uuid::v4(), 'discountPercentage' => 10],
        ]);

        self::assertSame(-32002, $error['code']);
        self::assertIsString($error['message']);
        self::assertStringContainsString('Cannot find "book"', $error['message']);
    }

    private function initialize(Client $client): void
    {
        $this->rpc($client, 'initialize', [
            'protocolVersion' => '2025-06-18',
            'capabilities' => [],
            'clientInfo' => ['name' => 'phpunit', 'version' => '1.0'],
        ]);

        $client->request('POST', '/mcp', [
            'headers' => $this->headers(),
            'json' => ['jsonrpc' => '2.0', 'method' => 'notifications/initialized'],
        ]);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function rpc(Client $client, string $method, array $params = []): array
    {
        $payload = $this->call($client, $method, $params);

        self::assertArrayNotHasKey('error', $payload, \json_encode($payload['error'] ?? null, \JSON_THROW_ON_ERROR));
        self::assertIsArray($payload['result']);

        /** @var array<string, mixed> $result */
        $result = $payload['result'];

        return $result;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function rpcError(Client $client, string $method, array $params = []): array
    {
        $payload = $this->call($client, $method, $params);

        self::assertArrayHasKey('error', $payload);
        self::assertIsArray($payload['error']);

        /** @var array<string, mixed> $error */
        $error = $payload['error'];

        return $error;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function call(Client $client, string $method, array $params): array
    {
        $response = $client->request('POST', '/mcp', [
            'headers' => $this->headers(),
            'json' => [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => $method,
                'params' => $params,
            ],
        ]);

        $sessionId = $response->getHeaders(false)['mcp-session-id'][0] ?? null;
        if (null !== $sessionId) {
            self::$sessionId = $sessionId;
        }

        /** @var array<string, mixed> */
        return $response->toArray(false);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/event-stream',
        ];

        if (null !== self::$sessionId) {
            $headers['Mcp-Session-Id'] = self::$sessionId;
        }

        return $headers;
    }
}
