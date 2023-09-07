<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\DecimalFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\DecimalFormatter
 */
class DecimalFormatterTest extends TestCase
{
    public function testBindValidPositiveValue()
    {
        self::assertSame('42.12', (new DecimalFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '42.12'])
        )->getValue());
    }

    public function testBindValidNegativeValue()
    {
        self::assertSame('-42.12', (new DecimalFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => '-42.12'])
        )->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new DecimalFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.float', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new DecimalFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindValidPositiveValue()
    {
        $data = (new DecimalFormatter())->unbind('foo', '42.12');
        self::assertSame('42.12', $data->getValue('foo'));
    }

    public function testUnbindValidNegativeValue()
    {
        $data = (new DecimalFormatter())->unbind('foo', '-42.12');
        self::assertSame('-42.12', $data->getValue('foo'));
    }

    public function testUnbindInvalidFloatValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new DecimalFormatter())->unbind('foo', 1.1);
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new DecimalFormatter())->unbind('foo', 'test');
    }
}
