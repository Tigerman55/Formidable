<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\TextFormatter;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\TextFormatter
 */
class TextFormatterTest extends TestCase
{
    public function testBindValidValue()
    {
        self::assertSame('bar', (new TextFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => 'bar'])
        )->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertTrue($bindResult->isSuccess());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindValidValue()
    {
        $data = (new TextFormatter())->unbind('foo', 'bar');
        self::assertSame('bar', $data->getValue('foo'));
    }

    public function testUnbindInvalidValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new TextFormatter())->unbind('foo', 1);
    }
}
