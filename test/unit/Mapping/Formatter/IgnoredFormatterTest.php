<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\IgnoredFormatter;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Formatter\IgnoredFormatter
 */
class IgnoredFormatterTest extends TestCase
{
    public function testBindValue()
    {
        self::assertSame(
            'foo',
            (new IgnoredFormatter('foo'))->bind('foo', Data::fromFlatArray(['foo' => 'baz']))->getValue()
        );
    }

    public function testUnbindValue()
    {
        $data = (new IgnoredFormatter('foo'))->unbind('foo', 'bar');
        self::assertTrue($data->isEmpty());
    }
}
