<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\BooleanFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(BooleanFormatter::class)]
class BooleanFormatterTest extends TestCase
{
    #[Test]
    public function bindValidTrueValue(): void
    {
        self::assertTrue((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'true']))->getValue());
    }

    #[Test]
    public function bindValidFalseValue(): void
    {
        self::assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'false']))->getValue());
    }

    #[Test]
    public function fallbackToFalseOnBindNonExistentKey(): void
    {
        self::assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray([]))->getValue());
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.boolean', $error->getMessage());
    }

    #[Test]
    public function unbindValidTrueValue(): void
    {
        $data = (new BooleanFormatter())->unbind('foo', true);
        self::assertSame('true', $data->getValue('foo'));
    }

    #[Test]
    public function unbindValidFalseValue(): void
    {
        $data = (new BooleanFormatter())->unbind('foo', false);
        self::assertSame('false', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidStringValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new BooleanFormatter())->unbind('foo', 'false');
    }
}
