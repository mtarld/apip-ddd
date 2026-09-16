<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

trait RunsAgainstDoctrine
{
    private static EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::$em = self::getContainer()->get(EntityManagerInterface::class);

        foreach (static::tablesToTruncate() as $table) {
            self::$em->getConnection()->executeStatement(\sprintf('TRUNCATE %s CASCADE', $table));
        }
    }

    protected function persist(): void
    {
        self::$em->flush();
    }

    protected function detach(): void
    {
        self::$em->clear();
    }

    /**
     * @return list<string>
     */
    abstract protected static function tablesToTruncate(): array;
}
