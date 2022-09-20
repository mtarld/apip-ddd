<?php

declare(strict_types=1);

namespace App\Tests\Subscription\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Subscription\Entity\Subscription;
use App\Tests\Subscription\DummySubscriptionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Uid\Uuid;

final class SubscriptionCrudTest extends ApiTestCase
{
    private static Connection $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$connection = static::getContainer()->get(Connection::class);

        (new Application(static::$kernel))
            ->find('doctrine:database:create')
            ->run(new ArrayInput(['--if-not-exists' => true]), new NullOutput());

        (new Application(static::$kernel))
            ->find('doctrine:schema:update')
            ->run(new ArrayInput(['--force' => true]), new NullOutput());
    }

    protected function setUp(): void
    {
        static::$connection->executeStatement('TRUNCATE subscription');
    }

    public function testCreateSubscription(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(Subscription::class);

        static::assertSame(0, $repository->count([]));

        $response = $client->request('POST', '/api/subscriptions', [
            'json' => [
                'email' => 'foo@bar.com',
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(Subscription::class);

        static::assertJsonContains([
            'email' => 'foo@bar.com',
        ]);

        $id = Uuid::fromString(str_replace('/api/subscriptions/', '', $response->toArray()['@id']));

        $subscription = $repository->find($id);

        static::assertNotNull($subscription);
        static::assertSame('foo@bar.com', $subscription->email);
    }

    public function testDeleteSubscription(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(Subscription::class);

        $subscription = DummySubscriptionFactory::createSubscription();

        $em->persist($subscription);
        $em->flush();

        static::assertSame(1, $repository->count([]));

        $response = $client->request('DELETE', sprintf('/api/subscriptions/%s', (string) $subscription->id));

        static::assertResponseIsSuccessful();
        static::assertEmpty($response->getContent());

        static::assertSame(0, $repository->count([]));
    }
}
