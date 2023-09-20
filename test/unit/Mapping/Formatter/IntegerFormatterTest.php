<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\IntegerFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(IntegerFormatter::class)]
class IntegerFormatterTest extends TestCase
{
    #[Test]
    public function bindValidPositiveValue(): void
    {
        self::assertSame(42, (new IntegerFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '42'])
        )->getValue());
    }

    #[Test]
    public function bindValidNegativeValue(): void
    {
        self::assertSame(-42, (new IntegerFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '-42'])
        )->getValue());
    }

    #[Test]
    public function bindInvalidFloatValue(): void
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray(['foo' => '1.1']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.integer', $error->message);
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.integer', $error->message);
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.required', $error->message);
    }

    #[Test]
    public function unbindValidPositiveValue(): void
    {
        $data = (new IntegerFormatter())->unbind('foo', 42);
        self::assertSame('42', $data->getValue('foo'));
    }

    #[Test]
    public function unbindValidNegativeValue(): void
    {
        $data = (new IntegerFormatter())->unbind('foo', -42);
        self::assertSame('-42', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidFloatValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new IntegerFormatter())->unbind('foo', 1.1);
    }
}
