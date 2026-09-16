<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RepositoryTestCase extends KernelTestCase
{
    protected function persist(): void
    {
    }

    protected function detach(): void
    {
    }
}
