<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\IntegerFormatter;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\IntegerFormatter
 */
class IntegerFormatterTest extends TestCase
{
    public function testBindValidPositiveValue()
    {
        self::assertSame(42, (new IntegerFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '42'])
        )->getValue());
    }

    public function testBindValidNegativeValue()
    {
        self::assertSame(-42, (new IntegerFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '-42'])
        )->getValue());
    }

    public function testBindInvalidFloatValue()
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray(['foo' => '1.1']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.integer', $error->getMessage());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.integer', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new IntegerFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindValidPositiveValue()
    {
        $data = (new IntegerFormatter())->unbind('foo', 42);
        self::assertSame('42', $data->getValue('foo'));
    }

    public function testUnbindValidNegativeValue()
    {
        $data = (new IntegerFormatter())->unbind('foo', -42);
        self::assertSame('-42', $data->getValue('foo'));
    }

    public function testUnbindInvalidFloatValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new IntegerFormatter())->unbind('foo', 1.1);
    }
}
