<?php

declare(strict_types=1);

namespace Test\Unit\FormError;

use Formidable\FormError\FormError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormError::class)]
class FormErrorTest extends TestCase
{
    #[Test]
    public function keyRetrieval(): void
    {
        self::assertSame('foo', (new FormError('foo', ''))->key);
    }

    #[Test]
    public function messageRetrieval(): void
    {
        self::assertSame('foo', (new FormError('', 'foo'))->message);
    }

    #[Test]
    public function argumentsRetrieval(): void
    {
        self::assertSame(['foo' => 'bar'], (new FormError('', '', ['foo' => 'bar']))->arguments);
    }
}
