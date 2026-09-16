<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\PurchasedBook;
use App\BookStore\Domain\ValueObject\Quantity;
use App\BookStore\Infrastructure\Doctrine\DoctrineOrderRepository;
use App\Tests\BookStore\Integration\OrderRepositoryTestCase;

final class DoctrineOrderRepositoryTest extends OrderRepositoryTestCase
{
    use RunsAgainstDoctrine;

    public function testLinesAreCascadedAndOrphanRemoved(): void
    {
        $repository = $this->repository();

        $order = Order::place(new OrderId(),
            new \DateTimeImmutable(self::PLACED_AT),
            new PurchasedBook(new BookId(), new BookName('Dune'), new Price(1500), new Quantity(1)),
        );

        $repository->add($order);
        $this->persist();

        self::assertSame(1, $this->countLines());

        self::$em->remove($order);
        $this->persist();

        self::assertSame(0, $this->countLines());
    }

    protected function repository(): OrderRepositoryInterface
    {
        return self::getContainer()->get(DoctrineOrderRepository::class);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['"order"'];
    }

    private function countLines(): int
    {
        $count = self::$em->getConnection()
            ->executeQuery('SELECT COUNT(*) FROM order_line')
            ->fetchOne();

        self::assertIsNumeric($count);

        return (int) $count;
    }
}
