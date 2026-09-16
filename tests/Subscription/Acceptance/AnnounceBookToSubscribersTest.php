<?php

declare(strict_types=1);

namespace App\Tests\Subscription\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\Subscription\SubscriptionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\TestHandler;

final class AnnounceBookToSubscribersTest extends ApiTestCase
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

    public function testAnnounceANewlyPublishedBook(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist(SubscriptionFactory::create('reader@example.com'));
        $em->persist(SubscriptionFactory::create('another@example.com'));
        $em->flush();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'Dune',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1500,
            ],
        ]);

        self::assertResponseIsSuccessful();

        /** @var TestHandler $testHandler */
        $testHandler = self::getContainer()->get('monolog.handler.subscription');
        self::assertTrue($testHandler->hasInfoThatContains('Announcing "Dune" (1500) to reader@example.com.'));
        self::assertTrue($testHandler->hasInfoThatContains('Announcing "Dune" (1500) to another@example.com.'));
    }
}
