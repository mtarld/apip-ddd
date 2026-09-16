<?php

declare(strict_types=1);

namespace App\Tests\Shared\Integration\Doctrine;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class EmbedValueObjectsDriverTest extends KernelTestCase
{
    public function testValueObjectPropertiesAreMappedAsEmbeddablesWithoutPrefix(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getClassMetadata(Book::class);

        foreach (['id' => BookId::class, 'name' => BookName::class, 'price' => Price::class] as $field => $class) {
            self::assertArrayHasKey($field, $metadata->embeddedClasses, \sprintf('"%s" is mapped as an embeddable', $field));
            self::assertSame($class, $metadata->embeddedClasses[$field]->class);
            self::assertFalse($metadata->embeddedClasses[$field]->columnPrefix, 'no column prefix');
        }

        self::assertSame('id', $metadata->getColumnName('id.value'));
        self::assertSame('name', $metadata->getColumnName('name.value'));
        self::assertSame('price', $metadata->getColumnName('price.value'));
    }

    public function testItLeavesEverythingElseAlone(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getClassMetadata(Book::class);

        self::assertArrayHasKey('reviewCollection', $metadata->associationMappings);
        self::assertArrayHasKey('classificationCollection', $metadata->associationMappings);

        self::assertArrayHasKey('authorIdValue', $metadata->fieldMappings);
        self::assertArrayNotHasKey('authorIdValue', $metadata->embeddedClasses);

        self::assertArrayNotHasKey('authorId', $metadata->fieldMappings);
        self::assertArrayNotHasKey('averageRating', $metadata->fieldMappings);
        self::assertArrayNotHasKey('reviews', $metadata->fieldMappings);
    }
}
