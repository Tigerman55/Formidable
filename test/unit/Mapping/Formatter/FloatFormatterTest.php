<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\FloatFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(FloatFormatter::class)]
class FloatFormatterTest extends TestCase
{
    #[Test]
    public function bindValidPositiveValue(): void
    {
        self::assertSame(42.12, (new FloatFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '42.12'])
        )->getValue());
    }

    #[Test]
    public function bindValidNegativeValue(): void
    {
        self::assertSame(-42.12, (new FloatFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '-42.12'])
        )->getValue());
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new FloatFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.float', $error->message);
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new FloatFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.required', $error->message);
    }

    #[Test]
    public function unbindValidPositiveValue(): void
    {
        $data = (new FloatFormatter())->unbind('foo', 42.12);
        self::assertSame('42.12', $data->getValue('foo'));
    }

    #[Test]
    public function unbindValidNegativeValue(): void
    {
        $data = (new FloatFormatter())->unbind('foo', -42.12);
        self::assertSame('-42.12', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidStringValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new FloatFormatter())->unbind('foo', '1.1');
    }
}
