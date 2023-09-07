<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\BooleanFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\BooleanFormatter
 */
class BooleanFormatterTest extends TestCase
{
    public function testBindValidTrueValue()
    {
        self::assertTrue((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'true']))->getValue());
    }

    public function testBindValidFalseValue()
    {
        self::assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'false']))->getValue());
    }

    public function testFallbackToFalseOnBindNonExistentKey()
    {
        self::assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray([]))->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.boolean', $error->getMessage());
    }

    public function testUnbindValidTrueValue()
    {
        $data = (new BooleanFormatter())->unbind('foo', true);
        self::assertSame('true', $data->getValue('foo'));
    }

    public function testUnbindValidFalseValue()
    {
        $data = (new BooleanFormatter())->unbind('foo', false);
        self::assertSame('false', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new BooleanFormatter())->unbind('foo', 'false');
    }
}
