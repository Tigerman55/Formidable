<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\ValidationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationError::class)]
class ValidationErrorTest extends TestCase
{
    #[Test]
    public function messageRetrieval(): void
    {
        self::assertSame('foo', (new ValidationError('foo'))->getMessage());
    }

    #[Test]
    public function argumentsRetrieval(): void
    {
        self::assertSame(['foo'], (new ValidationError('', ['foo']))->getArguments());
    }

    #[Test]
    public function keySuffixRetrieval(): void
    {
        self::assertSame('foo', (new ValidationError('', [], 'foo'))->getKeySuffix());
    }

    #[Test]
    public function defaults(): void
    {
        $validationError = new ValidationError('');
        self::assertSame([], $validationError->getArguments());
        self::assertSame('', $validationError->getKeySuffix());
    }
}
