<?php

declare(strict_types=1);

namespace App\Tests\Subscription\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Subscription\Entity\Subscription;
use App\Tests\Subscription\SubscriptionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class SubscriptionCrudTest extends ApiTestCase
{
    private static Connection $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$connection = self::getContainer()->get(Connection::class);
    }

    protected function setUp(): void
    {
        self::$connection->executeStatement('TRUNCATE subscription');
    }

    public function testCreateSubscription(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(Subscription::class);

        self::assertSame(0, $repository->count([]));

        $response = $client->request('POST', '/api/subscriptions', [
            'json' => [
                'email' => 'foo@bar.com',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(Subscription::class);

        self::assertJsonContains([
            'email' => 'foo@bar.com',
        ]);

        $iri = $response->toArray()['@id'];
        self::assertIsString($iri);
        $id = Uuid::fromString(\str_replace('/api/subscriptions/', '', $iri));

        $subscription = $repository->find($id);

        self::assertInstanceOf(Subscription::class, $subscription);
        self::assertSame('foo@bar.com', $subscription->email);
    }

    public function testDeleteSubscription(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(Subscription::class);

        $subscription = SubscriptionFactory::create();

        $em->persist($subscription);
        $em->flush();

        self::assertSame(1, $repository->count([]));

        $response = $client->request('DELETE', \sprintf('/api/subscriptions/%s', (string) $subscription->id));

        self::assertResponseIsSuccessful();
        self::assertEmpty($response->getContent());

        self::assertSame(0, $repository->count([]));
    }
}
