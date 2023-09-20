<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\TextFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(TextFormatter::class)]
class TextFormatterTest extends TestCase
{
    #[Test]
    public function bindValidValue(): void
    {
        self::assertSame('bar', (new TextFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => 'bar'])
        )->getValue());
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertTrue($bindResult->isSuccess());
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->key);
        self::assertSame('error.required', $error->message);
    }

    #[Test]
    public function unbindValidValue(): void
    {
        $data = (new TextFormatter())->unbind('foo', 'bar');
        self::assertSame('bar', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new TextFormatter())->unbind('foo', 1);
    }
}
