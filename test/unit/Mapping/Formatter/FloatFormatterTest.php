<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\FloatFormatter;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\FloatFormatter
 */
class FloatFormatterTest extends TestCase
{
    public function testBindValidPositiveValue()
    {
        self::assertSame(42.12, (new FloatFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '42.12'])
        )->getValue());
    }

    public function testBindValidNegativeValue()
    {
        self::assertSame(-42.12, (new FloatFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '-42.12'])
        )->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new FloatFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.float', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new FloatFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindValidPositiveValue()
    {
        $data = (new FloatFormatter())->unbind('foo', 42.12);
        self::assertSame('42.12', $data->getValue('foo'));
    }

    public function testUnbindValidNegativeValue()
    {
        $data = (new FloatFormatter())->unbind('foo', -42.12);
        self::assertSame('-42.12', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new FloatFormatter())->unbind('foo', '1.1');
    }
}
