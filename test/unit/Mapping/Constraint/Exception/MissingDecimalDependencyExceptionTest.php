<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\MissingDecimalDependencyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingDecimalDependencyException::class)]
class MissingDecimalDependencyExceptionTest extends TestCase
{
    #[Test]
    public function fromMissingDependency(): void
    {
        self::assertSame(
            'You must composer require litipk/php-bignumbers for this constraint to work',
            MissingDecimalDependencyException::fromMissingDependency()->getMessage()
        );
    }
}
